<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'path',
        'size',
        'mime_type',
        'user_id',
        'ticket_id',
        'conversation_id',
        'message_id'
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->firstOrFail();
    }

    public function message()
    {
        return $this->hasOne(Message::class);
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ticket(){
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function conversation(){
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    public function scopeOrderByName($query)
    {
        $query->orderBy('name');
    }


}
