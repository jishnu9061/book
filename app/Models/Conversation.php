<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model {
    use HasFactory;

    protected $fillable = [
        'title',
        'contact_id',
        'ticket_id',
        'type',
        'subject',
        'created_by',
        'context',
        'last_message_at',
        'status',
        'priority',
        'department',
        'source',
        'metadata',
        'last_activity'
    ];

    protected $casts = [
        'context' => 'array',
        'metadata' => 'array',
        'last_message_at' => 'datetime',
        'last_activity' => 'datetime',
    ];

    public function resolveRouteBinding($value, $field = null) {
        return $this->where($field ?? 'id', $value)->firstOrFail();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the main participant (contact) for backward compatibility
     * This is used by the existing ChatController
     */
    public function participant()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    /**
     * Get the contact associated with this conversation
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->whereHas('creator', function($q) use($search){
                $q->where('first_name', 'like', '%'.$search.'%')
                ->orWhere('last_name', 'like', '%'.$search.'%')
                ->orWhere('email', 'like', '%'.$search.'%');
            });
        });
    }
}
