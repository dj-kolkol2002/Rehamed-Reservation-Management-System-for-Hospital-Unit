<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'message',
        'type',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'metadata',
        'is_read',
        'read_at'
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    // Stałe typów wiadomości
    const TYPE_TEXT = 'text';
    const TYPE_IMAGE = 'image';
    const TYPE_FILE = 'file';
    const TYPE_EMOJI = 'emoji';

    /**
     * Relacje
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Relacja z nadawcą
     * Uwzględnia także usuniętych użytkowników (soft deleted) dla celów historycznych
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id')->withTrashed();
    }

    /**
     * Scopes
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeFromSender($query, $senderId)
    {
        return $query->where('sender_id', $senderId);
    }

    public function scopeInConversation($query, $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeWithFiles($query)
    {
        return $query->whereIn('type', [self::TYPE_IMAGE, self::TYPE_FILE]);
    }

    /**
     * Metody obsługi plików
     */
    public function hasFile()
    {
        return !empty($this->file_path) && Storage::disk('private')->exists($this->file_path);
    }

    public function isImage()
    {
        return $this->type === self::TYPE_IMAGE;
    }

    public function isFile()
    {
        return $this->type === self::TYPE_FILE;
    }

    public function isEmoji()
    {
        return $this->type === self::TYPE_EMOJI;
    }

    public function isText()
    {
        return $this->type === self::TYPE_TEXT;
    }

    /**
     * Pobierz URL do pliku
     */
    public function getFileUrlAttribute()
    {
        if ($this->hasFile()) {
            return route('chat.file.download', $this->id);
        }
        return null;
    }

    /**
     * Pobierz URL do miniaturki (dla obrazów)
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->isImage() && $this->hasFile()) {
            return route('chat.file.thumbnail', $this->id);
        }
        return null;
    }

    /**
     * Pobierz rozmiar pliku w czytelnym formacie
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) {
            return null;
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Pobierz ikonę dla typu pliku
     */
    public function getFileIconAttribute()
    {
        if (!$this->file_type) {
            return 'fas fa-file';
        }

        $iconMap = [
            // Obrazy
            'image/jpeg' => 'fas fa-file-image',
            'image/jpg' => 'fas fa-file-image',
            'image/png' => 'fas fa-file-image',
            'image/gif' => 'fas fa-file-image',
            'image/webp' => 'fas fa-file-image',
            'image/svg+xml' => 'fas fa-file-image',

            // Dokumenty
            'application/pdf' => 'fas fa-file-pdf',
            'application/msword' => 'fas fa-file-word',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'fas fa-file-word',
            'application/vnd.ms-excel' => 'fas fa-file-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'fas fa-file-excel',
            'application/vnd.ms-powerpoint' => 'fas fa-file-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'fas fa-file-powerpoint',

            // Archiwa
            'application/zip' => 'fas fa-file-archive',
            'application/x-rar-compressed' => 'fas fa-file-archive',
            'application/x-7z-compressed' => 'fas fa-file-archive',

            // Tekst
            'text/plain' => 'fas fa-file-alt',
            'text/csv' => 'fas fa-file-csv',

            // Audio
            'audio/mpeg' => 'fas fa-file-audio',
            'audio/wav' => 'fas fa-file-audio',
            'audio/mp3' => 'fas fa-file-audio',

            // Video
            'video/mp4' => 'fas fa-file-video',
            'video/avi' => 'fas fa-file-video',
            'video/mov' => 'fas fa-file-video',
        ];

        return $iconMap[$this->file_type] ?? 'fas fa-file';
    }

    /**
     * Sprawdź czy plik jest obrazem do wyświetlenia
     */
    public function canDisplayInline()
    {
        $allowedTypes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp'
        ];

        return $this->isImage() && in_array($this->file_type, $allowedTypes);
    }

    /**
     * Zapisz plik wiadomości
     */
    public function saveFile($file, $type = self::TYPE_FILE)
    {
        try {
            // Usuń stary plik jeśli istnieje
            $this->deleteFile();

            // Generuj unikalną nazwę pliku
            $extension = $file->getClientOriginalExtension();
            $filename = 'message_' . $this->id . '_' . time() . '_' . uniqid() . '.' . $extension;

            // Zapisz w katalogu chat-files
            $path = $file->storeAs('chat-files', $filename, 'private');

            if ($path) {
                $this->update([
                    'type' => $type,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);

                // Generuj miniaturkę dla obrazów
                if ($type === self::TYPE_IMAGE) {
                    $this->generateThumbnail();
                }

                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Usuń plik wiadomości
     */
    public function deleteFile()
    {
        if ($this->hasFile()) {
            Storage::disk('private')->delete($this->file_path);

            // Usuń miniaturkę jeśli istnieje
            $thumbnailPath = str_replace('chat-files/', 'chat-files/thumbnails/', $this->file_path);
            if (Storage::disk('private')->exists($thumbnailPath)) {
                Storage::disk('private')->delete($thumbnailPath);
            }

            $this->update([
                'file_path' => null,
                'file_name' => null,
                'file_type' => null,
                'file_size' => null,
                'type' => self::TYPE_TEXT
            ]);

            return true;
        }
        return false;
    }

    /**
     * Generuj miniaturkę obrazu
     */
    private function generateThumbnail()
    {
        if (!$this->isImage() || !$this->hasFile()) {
            return false;
        }

        try {
            $originalPath = Storage::disk('private')->path($this->file_path);
            $thumbnailDir = 'chat-files/thumbnails';
            $thumbnailName = pathinfo($this->file_path, PATHINFO_FILENAME) . '_thumb.' . pathinfo($this->file_path, PATHINFO_EXTENSION);
            $thumbnailPath = $thumbnailDir . '/' . $thumbnailName;

            // Utwórz katalog jeśli nie istnieje
            Storage::disk('private')->makeDirectory($thumbnailDir);

            $thumbnailFullPath = Storage::disk('private')->path($thumbnailPath);

            // Sprawdź czy GD extension jest dostępne
            if (!extension_loaded('gd')) {
                return false;
            }

            // Utwórz miniaturkę o wymiarach 300x300
            $this->createThumbnail($originalPath, $thumbnailFullPath, 300, 300);

            // Zapisz ścieżkę miniaturki w metadata
            $metadata = $this->metadata ?? [];
            $metadata['thumbnail_path'] = $thumbnailPath;
            $this->update(['metadata' => $metadata]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Utwórz miniaturkę obrazu
     */
    private function createThumbnail($sourcePath, $destPath, $maxWidth, $maxHeight)
    {
        $imageInfo = getimagesize($sourcePath);
        $mime = $imageInfo['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            default:
                return false;
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);

        // Oblicz nowe wymiary zachowując proporcje
        $ratio = min($maxWidth / $sourceWidth, $maxHeight / $sourceHeight);
        $newWidth = round($sourceWidth * $ratio);
        $newHeight = round($sourceHeight * $ratio);

        $destImage = imagecreatetruecolor($newWidth, $newHeight);

        // Zachowaj przezroczystość dla PNG i GIF
        if ($mime === 'image/png' || $mime === 'image/gif') {
            imagealphablending($destImage, false);
            imagesavealpha($destImage, true);
            $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
            imagefill($destImage, 0, 0, $transparent);
        }

        imagecopyresampled($destImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);

        switch ($mime) {
            case 'image/jpeg':
                imagejpeg($destImage, $destPath, 90);
                break;
            case 'image/png':
                imagepng($destImage, $destPath, 9);
                break;
            case 'image/gif':
                imagegif($destImage, $destPath);
                break;
        }

        imagedestroy($sourceImage);
        imagedestroy($destImage);

        return true;
    }

    /**
     * Oznacz wiadomość jako przeczytaną
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
    }

    /**
     * Sprawdź czy wiadomość jest przeczytana
     */
    public function isRead()
    {
        return $this->is_read;
    }

    /**
     * Atrybuty pomocnicze
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d.m.Y H:i');
    }

    public function getRelativeTimeAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getShortMessageAttribute()
    {
        if ($this->type !== self::TYPE_TEXT) {
            return $this->getTypeDisplayName();
        }

        return strlen($this->message) > 50
            ? substr($this->message, 0, 50) . '...'
            : $this->message;
    }

    /**
     * Pobierz wyświetlaną nazwę typu wiadomości
     */
    public function getTypeDisplayName()
    {
        $types = [
            self::TYPE_TEXT => 'Wiadomość tekstowa',
            self::TYPE_IMAGE => '📷 Obraz',
            self::TYPE_FILE => '📎 Plik',
            self::TYPE_EMOJI => 'Emotka'
        ];

        return $types[$this->type] ?? 'Nieznany typ';
    }

    /**
     * Sprawdź czy wiadomość została wysłana dzisiaj
     */
    public function isSentToday()
    {
        return $this->created_at->isToday();
    }

    /**
     * Sprawdź czy wiadomość została wysłana przez określonego użytkownika
     */
    public function isSentBy($userId)
    {
        return $this->sender_id === $userId;
    }

    /**
     * Pobierz status doręczenia wiadomości
     */
    public function getDeliveryStatusAttribute()
    {
        if ($this->is_read) {
            return 'przeczytana';
        }

        return 'dostarczona';
    }

    /**
     * Pobierz ikonę statusu
     */
    public function getStatusIconAttribute()
    {
        if ($this->is_read) {
            return 'fas fa-check-double text-blue-400';
        }

        return 'fas fa-check text-gray-400';
    }

    /**
     * Sprawdź czy wiadomość może zostać usunięta przez użytkownika
     */
    public function canBeDeletedBy($userId)
    {
        // Tylko nadawca może usunąć wiadomość w ciągu pierwszych 5 minut
        return $this->sender_id === $userId &&
               $this->created_at->diffInMinutes(now()) <= 5;
    }

    /**
     * Sprawdź czy wiadomość może zostać edytowana przez użytkownika
     */
    public function canBeEditedBy($userId)
    {
        // Tylko tekstowe wiadomości można edytować
        // Tylko nadawca może edytować wiadomość w ciągu pierwszych 5 minut
        // i tylko jeśli nie została jeszcze przeczytana
        return $this->type === self::TYPE_TEXT &&
               $this->sender_id === $userId &&
               $this->created_at->diffInMinutes(now()) <= 5 &&
               !$this->is_read;
    }

    /**
     * Boot method - automatyczne aktualizowanie czasu ostatniej wiadomości
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($message) {
            $message->conversation->updateLastMessageTime();
        });

        static::updated(function ($message) {
            $message->conversation->updateLastMessageTime();
        });

        static::deleting(function ($message) {
            // Usuń plik przy usuwaniu wiadomości
            $message->deleteFile();
        });
    }
}
