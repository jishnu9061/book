<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'contact_id',
        'message',
        'message_type',
        'is_read',
        'read_at',
        'is_internal'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'is_internal' => 'boolean',
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->firstOrFail();
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function scopeOrderByMessage($query)
    {
        $query->orderBy('message');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('message', 'like', '%'.$search.'%');
            });
        });
    }

    /**
     * Get formatted message for display
     */
    public function getFormattedMessageAttribute(): string
    {
        return \App\Helpers\ChatHelper::formatMessageForDisplay($this->message);
    }

    /**
     * Get sender name
     */
    public function getSenderNameAttribute(): string
    {
        return \App\Helpers\ChatHelper::getMessageSenderName($this);
    }

    /**
     * Check if message is from user
     */
    public function isFromUser($userId): bool
    {
        return \App\Helpers\ChatHelper::isMessageFromUser($this, $userId);
    }
}
