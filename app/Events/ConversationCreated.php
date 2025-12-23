<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Conversation;

class ConversationCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversation;
    public $participants;
    public $creator;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Conversation $conversation, array $participants, $creator = null)
    {
        $this->conversation = $conversation;
        $this->participants = $participants;
        $this->creator = $creator;
        
        \Log::info('ConversationCreated Event: Created', [
            'conversation_id' => $conversation->id,
            'conversation_type' => $conversation->type,
            'participants_count' => count($participants),
            'creator_id' => $creator?->id
        ]);
    }
}





