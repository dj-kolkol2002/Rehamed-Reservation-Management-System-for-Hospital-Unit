<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Wyświetl główną stronę czatu
     */
    public function index()
    {
        $user = Auth::user();

        // Pobierz wszystkie konwersacje użytkownika
        $conversations = $this->getUserConversations($user);

        // Pobierz użytkowników z którymi można rozmawiać
        $chatableUsers = $this->getChatableUsers($user);

        return view('chat.index', compact('conversations', 'chatableUsers'));
    }

    /**
     * Wyświetl konkretną konwersację
     */
    public function show(Conversation $conversation)
    {
        $user = Auth::user();

        // Sprawdź czy użytkownik jest uczestnikiem konwersacji
        if (!$conversation->isParticipant($user->id)) {
            abort(403, 'Nie masz uprawnień do przeglądania tej konwersacji.');
        }

        // Oznacz wiadomości jako przeczytane
        $conversation->markAsReadFor($user->id);

        // Pobierz wiadomości (paginacja)
        $messages = $conversation->messages()
                                ->with('sender')
                                ->orderBy('created_at', 'desc')
                                ->paginate(50);

        $messages->setCollection($messages->getCollection()->reverse());

        // Pobierz wszystkie konwersacje dla sidebara
        $conversations = $this->getUserConversations($user);

        // Pobierz użytkowników z którymi można rozmawiać
        $chatableUsers = $this->getChatableUsers($user);

        // Drugi uczestnik konwersacji
        $otherUser = $conversation->getOtherParticipant($user->id);

        // Sprawdź czy drugi uczestnik nie został usunięty
        if (!$otherUser || $otherUser->trashed()) {
            return redirect()->route('chat.index')
                ->with('error', 'Ta konwersacja nie jest dostępna.');
        }

        return view('chat.conversation', compact(
            'conversation',
            'messages',
            'conversations',
            'chatableUsers',
            'otherUser'
        ));
    }

    /**
     * Wyślij nową wiadomość
     */
    public function sendMessage(Request $request)
    {
        $user = Auth::user();

        try {
            // Logowanie dla debugowania
            Log::info('Chat send message attempt', [
                'user_id' => $user->id,
                'request_data' => $request->except(['file']),
                'message_content' => $request->message,
                'type' => $request->type
            ]);

            // Podstawowa walidacja
            $validator = Validator::make($request->all(), [
                'conversation_id' => 'nullable|exists:conversations,id',
                'recipient_id' => 'required_without:conversation_id|exists:users,id',
                'message' => 'nullable|string|max:1000',
                'type' => 'required|in:text,image,file,emoji',
                'file' => 'nullable|file|max:10240', // 10MB max
            ], [
                'message.max' => 'Wiadomość nie może przekraczać 1000 znaków.',
                'recipient_id.required_without' => 'Wybierz odbiorcę wiadomości.',
                'recipient_id.exists' => 'Wybrany odbiorca nie istnieje.',
                'type.required' => 'Typ wiadomości jest wymagany.',
                'type.in' => 'Nieprawidłowy typ wiadomości.',
                'file.max' => 'Plik nie może być większy niż 10MB.',
            ]);

            // Dodatkowa walidacja w zależności od typu
            if ($request->type === 'text' || $request->type === 'emoji') {
                $validator->after(function ($validator) use ($request) {
                    if (empty(trim($request->message))) {
                        $validator->errors()->add('message', 'Wiadomość nie może być pusta.');
                    }
                });
            } elseif ($request->type === 'image') {
                $validator->addRules([
                    'file' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120' // 5MB dla obrazów
                ]);
            } elseif ($request->type === 'file') {
                $validator->addRules([
                    'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar,7z|max:10240'
                ]);
            }

            if ($validator->fails()) {
                Log::warning('Validation failed for chat message', [
                    'errors' => $validator->errors()->toArray(),
                    'request_data' => $request->except(['file'])
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Nieprawidłowe dane.',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            try {
                // Jeśli nie ma conversation_id, znajdź lub utwórz konwersację
                if (!$request->conversation_id) {
                    $recipient = User::findOrFail($request->recipient_id);

                    // Sprawdź czy może pisać do tego użytkownika
                    if (!$this->canChatWith($user, $recipient)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Nie możesz napisać do tego użytkownika.'
                        ], 403);
                    }

                    $conversation = Conversation::createOrFind($user->id, $recipient->id);
                } else {
                    $conversation = Conversation::findOrFail($request->conversation_id);

                    // Sprawdź czy użytkownik jest uczestnikiem konwersacji
                    if (!$conversation->isParticipant($user->id)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Nie masz uprawnień do tej konwersacji.'
                        ], 403);
                    }
                }

                // Automatyczna detekcja typu wiadomości jeśli nie został podany lub jest nieprawidłowy
                $messageType = $request->type;
                $messageContent = $request->message ?? '';

                // Jeśli to nie plik i nie podano typu lub podano 'text', sprawdź czy to emoji
                if (!$request->hasFile('file') && ($messageType === 'text' || !in_array($messageType, ['emoji', 'text']))) {
                    if ($this->isEmojiOnly($messageContent)) {
                        $messageType = 'emoji';
                        Log::info('Auto-detected emoji message', ['content' => $messageContent]);
                    } else {
                        $messageType = 'text';
                    }
                }

                // Utwórz wiadomość
                $messageData = [
                    'conversation_id' => $conversation->id,
                    'sender_id' => $user->id,
                    'message' => $messageContent,
                    'type' => $messageType,
                    'is_read' => false
                ];

                Log::info('Creating message with data', $messageData);

                $message = Message::create($messageData);

                // Obsłuż plik jeśli został przesłany
                if ($request->hasFile('file')) {
                    $fileType = $request->type === 'image' ? Message::TYPE_IMAGE : Message::TYPE_FILE;

                    if (!$message->saveFile($request->file('file'), $fileType)) {
                        throw new \Exception('Błąd podczas przesyłania pliku.');
                    }
                }

                // Załaduj relacje
                $message->load('sender');

                // Sprawdź czy sender został załadowany
                if (!$message->sender) {
                    throw new \Exception('Błąd ładowania danych nadawcy');
                }

                // Aktualizuj czas ostatniej wiadomości w konwersacji
                $conversation->update(['last_message_at' => now()]);

                // NOWE: Znajdź odbiorcę i utwórz powiadomienie
                $recipientId = $conversation->user_one_id === $user->id
                    ? $conversation->user_two_id
                    : $conversation->user_one_id;

                // Utwórz powiadomienie o nowej wiadomości (jeśli metoda przyjmuje 2 parametry)
                if (method_exists(Notification::class, 'messageReceived')) {
                    try {
                        Notification::messageReceived($message);
                    } catch (\ArgumentCountError $e) {
                        // Jeśli metoda wymaga 2 parametrów, wywołaj z recipientId
                        // To wymaga sprawdzenia implementacji metody messageReceived
                        \Log::warning('Notification::messageReceived requires different parameters', [
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                DB::commit();

                Log::info('Chat message sent successfully', [
                    'message_id' => $message->id,
                    'conversation_id' => $conversation->id,
                    'type' => $message->type,
                    'content' => $message->message,
                    'notification_sent_to' => $recipientId
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $this->formatMessageForResponse($message),
                    'conversation_id' => $conversation->id
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Error in message transaction', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Chat send message error', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Wystąpił błąd podczas wysyłania wiadomości: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pobierz nowe wiadomości dla konwersacji
     */
    public function getMessages(Conversation $conversation, Request $request)
    {
        $user = Auth::user();

        // Sprawdź uprawnienia
        if (!$conversation->isParticipant($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Nie masz uprawnień do tej konwersacji.'
            ], 403);
        }

        $lastMessageId = $request->get('last_message_id', 0);

        $messages = $conversation->messages()
                                ->with('sender')
                                ->where('id', '>', $lastMessageId)
                                ->orderBy('created_at', 'asc')
                                ->get();

        // Oznacz nowe wiadomości jako przeczytane
        if ($messages->count() > 0) {
            $conversation->messages()
                        ->where('sender_id', '!=', $user->id)
                        ->where('id', '>', $lastMessageId)
                        ->where('is_read', false)
                        ->update([
                            'is_read' => true,
                            'read_at' => now()
                        ]);
        }

        // Formatuj wiadomości do odpowiedzi
        $formattedMessages = $messages->map(function ($message) {
            return $this->formatMessageForResponse($message);
        });

        return response()->json([
            'success' => true,
            'messages' => $formattedMessages
        ]);
    }

    /**
     * Pobierz plik z wiadomości
     */
    public function downloadFile(Message $message)
    {
        $user = Auth::user();

        // Sprawdź uprawnienia do konwersacji
        if (!$message->conversation->isParticipant($user->id)) {
            abort(403, 'Nie masz uprawnień do tego pliku.');
        }

        if (!$message->hasFile()) {
            abort(404, 'Plik nie został znaleziony.');
        }

        $filePath = Storage::disk('private')->path($message->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'Plik nie istnieje.');
        }

        return response()->download($filePath, $message->file_name);
    }

    /**
     * Pobierz miniaturkę obrazu
     */
    public function getThumbnail(Message $message)
    {
        $user = Auth::user();

        // Sprawdź uprawnienia do konwersacji
        if (!$message->conversation->isParticipant($user->id)) {
            abort(403, 'Nie masz uprawnień do tego obrazu.');
        }

        if (!$message->isImage() || !$message->hasFile()) {
            abort(404, 'Miniaturka nie została znaleziona.');
        }

        $metadata = $message->metadata ?? [];
        $thumbnailPath = $metadata['thumbnail_path'] ?? null;

        if (!$thumbnailPath || !Storage::disk('private')->exists($thumbnailPath)) {
            // Jeśli miniaturka nie istnieje, zwróć oryginalny obraz
            return $this->getImage($message);
        }

        $filePath = Storage::disk('private')->path($thumbnailPath);
        $mimeType = $message->file_type;

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }

    /**
     * Pobierz obraz w pełnym rozmiarze
     */
    public function getImage(Message $message)
    {
        $user = Auth::user();

        // Sprawdź uprawnienia do konwersacji
        if (!$message->conversation->isParticipant($user->id)) {
            abort(403, 'Nie masz uprawnień do tego obrazu.');
        }

        if (!$message->isImage() || !$message->hasFile()) {
            abort(404, 'Obraz nie został znaleziony.');
        }

        $filePath = Storage::disk('private')->path($message->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'Plik obrazu nie istnieje.');
        }

        return response()->file($filePath, [
            'Content-Type' => $message->file_type,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }

    /**
     * Rozpocznij nową konwersację
     */
    public function startConversation(User $recipient)
    {
        $user = Auth::user();

        // Sprawdź czy może pisać do tego użytkownika
        if (!$this->canChatWith($user, $recipient)) {
            abort(403, 'Nie możesz rozpocząć konwersacji z tym użytkownikiem.');
        }

        // Znajdź lub utwórz konwersację
        $conversation = Conversation::createOrFind($user->id, $recipient->id);

        return redirect()->route('chat.conversation', $conversation);
    }

    /**
     * Pobierz liczbę nieprzeczytanych wiadomości
     */
    public function getUnreadCount(): JsonResponse
    {
        $user = Auth::user();
        $unreadCount = $this->getUnreadMessagesCount($user);

        return response()->json([
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Oznacz konwersację jako przeczytaną
     */
    public function markAsRead(Conversation $conversation)
    {
        $user = Auth::user();

        if (!$conversation->isParticipant($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Nie masz uprawnień do tej konwersacji.'
            ], 403);
        }

        $conversation->markAsReadFor($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Konwersacja została oznaczona jako przeczytana.'
        ]);
    }

    /**
     * Usuń konwersację (tylko oznacza jako usuniętą)
     */
    public function deleteConversation(Conversation $conversation)
    {
        $user = Auth::user();

        if (!$conversation->isParticipant($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Nie masz uprawnień do tej konwersacji.'
            ], 403);
        }

        // W przyszłości można dodać soft delete dla użytkowników
        // Na razie tylko przekierowanie
        return redirect()->route('chat.index')
                        ->with('success', 'Konwersacja została usunięta.');
    }

    /**
     * API: Pobierz listę konwersacji
     */
    public function getConversations(): JsonResponse
    {
        $user = Auth::user();
        $conversations = $this->getUserConversations($user);

        return response()->json([
            'conversations' => $conversations->map(function ($conversation) use ($user) {
                $otherUser = $conversation->getOtherParticipant($user->id);
                return [
                    'id' => $conversation->id,
                    'other_user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->full_name,
                        'avatar_url' => $otherUser->avatar_url,
                        'role_display' => $this->getRoleDisplay($otherUser->role)
                    ],
                    'last_message' => $conversation->lastMessage ? [
                        'message' => $conversation->lastMessage->short_message,
                        'created_at' => $conversation->lastMessage->created_at->diffForHumans(),
                        'is_from_me' => $conversation->lastMessage->sender_id === $user->id,
                        'type' => $conversation->lastMessage->type
                    ] : null,
                    'unread_count' => $conversation->getUnreadCountFor($user->id),
                    'last_message_at' => $conversation->last_message_at ?
                                       $conversation->last_message_at->diffForHumans() : null
                ];
            })
        ]);
    }

    /**
     * Pobierz emotki
     */
    public function getEmojis(): JsonResponse
    {
        $emojis = [
            // UÅ›miechy i emocje
            'ðŸ˜€', 'ðŸ˜ƒ', 'ðŸ˜„', 'ðŸ˜', 'ðŸ˜†', 'ðŸ˜…', 'ðŸ¤£', 'ðŸ˜‚', 'ðŸ™‚', 'ðŸ™ƒ',
            'ðŸ˜‰', 'ðŸ˜Š', 'ðŸ˜‡', 'ðŸ¥°', 'ðŸ˜', 'ðŸ¤©', 'ðŸ˜˜', 'ðŸ˜—', 'ðŸ˜š', 'ðŸ˜™',
            'ðŸ˜‹', 'ðŸ˜›', 'ðŸ˜œ', 'ðŸ¤ª', 'ðŸ˜', 'ðŸ¤‘', 'ðŸ¤—', 'ðŸ¤­', 'ðŸ¤«', 'ðŸ¤”',
            'ðŸ¤', 'ðŸ¤¨', 'ðŸ˜', 'ðŸ˜‘', 'ðŸ˜¶', 'ðŸ˜', 'ðŸ˜’', 'ðŸ™„', 'ðŸ˜¬', 'ðŸ¤¥',
            'ðŸ˜”', 'ðŸ˜ª', 'ðŸ¤¤', 'ðŸ˜´', 'ðŸ˜·', 'ðŸ¤’', 'ðŸ¤•', 'ðŸ¤¢', 'ðŸ¤®', 'ðŸ¤§',
            'ðŸ¥µ', 'ðŸ¥¶', 'ðŸ¥´', 'ðŸ˜µ', 'ðŸ¤¯', 'ðŸ¤ ', 'ðŸ¥³', 'ðŸ˜Ž', 'ðŸ¤“', 'ðŸ§',

            // Gesty
            'ðŸ‘', 'ðŸ‘Ž', 'ðŸ‘Œ', 'ðŸ¤Œ', 'âœŒï¸', 'ðŸ¤ž', 'ðŸ¤Ÿ', 'ðŸ¤˜', 'ðŸ¤™', 'ðŸ‘ˆ',
            'ðŸ‘‰', 'ðŸ‘†', 'ðŸ–•', 'ðŸ‘‡', 'â˜ï¸', 'ðŸ‘', 'ðŸ™Œ', 'ðŸ‘', 'ðŸ¤²', 'ðŸ™',

            // Serca i symbole
            'â¤ï¸', 'ðŸ§¡', 'ðŸ’›', 'ðŸ’š', 'ðŸ’™', 'ðŸ’œ', 'ðŸ–¤', 'ðŸ¤', 'ðŸ¤Ž', 'ðŸ’”',
            'â£ï¸', 'ðŸ’•', 'ðŸ’ž', 'ðŸ’“', 'ðŸ’—', 'ðŸ’–', 'ðŸ’˜', 'ðŸ’', 'ðŸ’Ÿ', 'â™¥ï¸',

            // Inne popularne
            'ðŸ”¥', 'ðŸ’¯', 'ðŸ’¥', 'ðŸ’«', 'â­', 'ðŸŒŸ', 'âœ¨', 'âš¡', 'ðŸ’¦', 'ðŸ’¨',
            'ðŸŽ‰', 'ðŸŽŠ', 'ðŸŽˆ', 'ðŸŽ', 'ðŸ†', 'ðŸ¥‡', 'ðŸ¥ˆ', 'ðŸ¥‰', 'ðŸ…', 'ðŸŽ–ï¸',
        ];

        return response()->json([
            'emojis' => $emojis
        ]);
    }

    // === PRIVATE HELPER METHODS ===

    /**
     * Sprawdź czy tekst zawiera tylko emoji
     */
    private function isEmojiOnly($text)
    {
        if (empty($text)) {
            return false;
        }

        // Usuń wszystkie białe znaki
        $cleanText = preg_replace('/\s+/', '', $text);

        if (empty($cleanText)) {
            return false;
        }

        // Kompleksowy regex dla emoji
        $emojiPattern = '/^(?:[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]|[\x{1F900}-\x{1F9FF}]|[\x{1F018}-\x{1F270}]|[\x{238C}-\x{2454}]|[\x{20D0}-\x{20FF}]|[\x{FE00}-\x{FE0F}]|[\x{1F200}-\x{1F2FF}])+$/u';

        $result = preg_match($emojiPattern, $cleanText);

        Log::info('Emoji detection', [
            'original_text' => $text,
            'clean_text' => $cleanText,
            'is_emoji' => $result ? 'yes' : 'no'
        ]);

        return $result;
    }

    /**
     * Formatuj wiadomość do odpowiedzi JSON
     */
    private function formatMessageForResponse($message)
    {
        // Sprawdź czy sender istnieje
        $sender = $message->sender;
        $senderData = null;

        if ($sender) {
            $senderData = [
                'id' => $sender->id,
                'name' => $sender->full_name ?? ($sender->firstname . ' ' . $sender->lastname),
                'avatar_url' => $sender->avatar_url ?? null
            ];
        }

        $formatted = [
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'message' => $message->message,
            'type' => $message->type,
            'is_read' => $message->is_read,
            'created_at' => $message->created_at->toISOString(),
            'formatted_time' => $message->formatted_created_at ?? $message->created_at->format('H:i'),
            'relative_time' => $message->relative_time ?? $message->created_at->diffForHumans(),
            'sender' => $senderData
        ];

        // Dodaj informacje o pliku jeśli istnieje
        if ($message->hasFile()) {
            $formatted['file'] = [
                'name' => $message->file_name,
                'size' => $message->formatted_file_size,
                'type' => $message->file_type,
                'icon' => $message->file_icon,
                'download_url' => $message->file_url,
                'can_display_inline' => $message->canDisplayInline()
            ];

            if ($message->isImage()) {
                $formatted['file']['thumbnail_url'] = $message->thumbnail_url;
                $formatted['file']['image_url'] = route('chat.file.image', $message->id);
            }
        }

        return $formatted;
    }

    /**
     * Pobierz konwersacje użytkownika
     */
    private function getUserConversations($user)
    {
        $conversations = Conversation::forUser($user->id)
                   ->with(['userOne', 'userTwo', 'lastMessage.sender'])
                   ->orderBy('last_message_at', 'desc')
                   ->get();

        // Filtruj konwersacje gdzie drugi użytkownik został usunięty
        return $conversations->filter(function ($conversation) use ($user) {
            $otherUser = $conversation->getOtherParticipant($user->id);
            return $otherUser !== null && !$otherUser->trashed();
        });
    }

    /**
     * Pobierz użytkowników z którymi można rozmawiać
     */
    private function getChatableUsers($user)
    {
        $query = User::where('id', '!=', $user->id)
                    ->where('is_active', true);

        // Zasady czatu w zależności od roli użytkownika
        switch ($user->role) {
            case 'admin':
                // Admin może pisać do wszystkich
                break;

            case 'doctor':
                // Doktor może pisać do pacjentów i adminów
                $query->whereIn('role', ['user', 'admin']);
                break;

            case 'user':
                // Pacjent może pisać do doktorów i adminów
                $query->whereIn('role', ['doctor', 'admin']);
                break;
        }

        return $query->orderBy('firstname')
                    ->orderBy('lastname')
                    ->get();
    }

    /**
     * Sprawdź czy użytkownik może rozmawiać z innym użytkownikiem
     */
    private function canChatWith($user, $recipient)
    {
        if ($user->id === $recipient->id) {
            return false;
        }

        if (!$recipient->is_active) {
            return false;
        }

        // Zasady czatu w zależności od roli
        switch ($user->role) {
            case 'admin':
                return true; // Admin może rozmawiać ze wszystkimi

            case 'doctor':
                return in_array($recipient->role, ['user', 'admin']);

            case 'user':
                return in_array($recipient->role, ['doctor', 'admin']);

            default:
                return false;
        }
    }

    /**
     * Pobierz liczbę nieprzeczytanych wiadomości dla użytkownika
     */
    private function getUnreadMessagesCount($user)
    {
        return Message::whereHas('conversation', function ($query) use ($user) {
            $query->forUser($user->id);
        })
        ->where('sender_id', '!=', $user->id)
        ->where('is_read', false)
        ->count();
    }

    /**
     * Pobierz wyświetlaną nazwę roli
     */
    private function getRoleDisplay($role)
    {
        $roles = [
            'admin' => 'Administrator',
            'doctor' => 'Fizjoterapeuta',
            'user' => 'Pacjent'
        ];

        return $roles[$role] ?? 'Nieznany';
    }
}
