<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_one_id',
        'user_two_id',
        'last_message_at'
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    /**
     * Relacje
     * Uwzględniają także usuniętych użytkowników (soft deleted) dla celów historycznych
     */
    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id')->withTrashed();
    }

    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id')->withTrashed();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    /**
     * Pobierz nieprzeczytane wiadomoÅ›ci dla uÅ¼ytkownika
     */
    public function unreadMessagesFor($userId)
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false);
    }

    /**
     * Pobierz drugiego uczestnika konwersacji
     * UWAGA: Zwraca również usuniętych użytkowników dla celów historycznych
     */
    public function getOtherParticipant($userId)
    {
        if ($this->user_one_id == $userId) {
            return $this->userTwo;
        } elseif ($this->user_two_id == $userId) {
            return $this->userOne;
        }

        return null;
    }

    /**
     * Alias dla getOtherParticipant - dla kompatybilności
     */
    public function getOtherUser($userId)
    {
        return $this->getOtherParticipant($userId);
    }

    /**
     * Alias dla getUnreadCountFor - dla kompatybilności
     */
    public function getUnreadCountForUser($userId)
    {
        return $this->getUnreadCountFor($userId);
    }

    /**
     * SprawdÅº czy uÅ¼ytkownik jest uczestnikiem konwersacji
     */
    public function isParticipant($userId)
    {
        return $this->user_one_id == $userId || $this->user_two_id == $userId;
    }

    /**
     * ZnajdÅº konwersacjÄ™ miÄ™dzy dwoma uÅ¼ytkownikami
     */
    public static function findBetweenUsers($userOneId, $userTwoId)
    {
        return self::where(function ($query) use ($userOneId, $userTwoId) {
            $query->where('user_one_id', $userOneId)
                  ->where('user_two_id', $userTwoId);
        })->orWhere(function ($query) use ($userOneId, $userTwoId) {
            $query->where('user_one_id', $userTwoId)
                  ->where('user_two_id', $userOneId);
        })->first();
    }

    /**
     * UtwÃ³rz lub znajdÅº konwersacjÄ™ miÄ™dzy uÅ¼ytkownikami
     */
    public static function createOrFind($userOneId, $userTwoId)
    {
        // SprawdÅº czy konwersacja juÅ¼ istnieje
        $conversation = self::findBetweenUsers($userOneId, $userTwoId);

        if ($conversation) {
            return $conversation;
        }

        // Upewnij siÄ™, Å¼e user_one_id jest mniejsze od user_two_id dla konsystencji
        if ($userOneId > $userTwoId) {
            $temp = $userOneId;
            $userOneId = $userTwoId;
            $userTwoId = $temp;
        }

        return self::create([
            'user_one_id' => $userOneId,
            'user_two_id' => $userTwoId,
            'last_message_at' => now()
        ]);
    }

    /**
     * Scope dla konwersacji uÅ¼ytkownika
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('user_one_id', $userId)
              ->orWhere('user_two_id', $userId);
        });
    }

    /**
     * Zaktualizuj czas ostatniej wiadomoÅ›ci
     */
    public function updateLastMessageTime()
    {
        $this->update(['last_message_at' => now()]);
    }

    /**
     * Oznacz wszystkie wiadomoÅ›ci jako przeczytane dla uÅ¼ytkownika
     */
    public function markAsReadFor($userId)
    {
        $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }

    /**
     * Pobierz liczbÄ™ nieprzeczytanych wiadomoÅ›ci dla uÅ¼ytkownika
     */
    public function getUnreadCountFor($userId)
    {
        return $this->unreadMessagesFor($userId)->count();
    }

    /**
     * Pobierz czas ostatniej aktywnoÅ›ci w konwersacji
     */
    public function getLastActivityAttribute()
    {
        return $this->last_message_at ? $this->last_message_at->diffForHumans() : 'Brak aktywnoÅ›ci';
    }

    /**
     * SprawdÅº czy konwersacja ma nieprzeczytane wiadomoÅ›ci dla uÅ¼ytkownika
     */
    public function hasUnreadMessagesFor($userId)
    {
        return $this->getUnreadCountFor($userId) > 0;
    }

    /**
     * Boot method - automatyczne Å‚adowanie relacji
     */
    protected static function boot()
    {
        parent::boot();

        static::retrieved(function ($conversation) {
            // Automatycznie zaÅ‚aduj relacje jeÅ›li nie sÄ… zaÅ‚adowane
            if (!$conversation->relationLoaded('userOne')) {
                $conversation->load('userOne');
            }
            if (!$conversation->relationLoaded('userTwo')) {
                $conversation->load('userTwo');
            }
        });
    }
}
