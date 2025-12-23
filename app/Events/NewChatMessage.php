<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

class NewChatMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Message
     */
    public $chatMessage;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( Message $chatMessage)
    {
        $this->chatMessage = $chatMessage;
        
        \Log::info('NewChatMessage Event: Created', [
            'message_id' => $chatMessage->id,
            'conversation_id' => $chatMessage->conversation_id,
            'user_id' => $chatMessage->user_id,
            'contact_id' => $chatMessage->contact_id
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() {
        $channelName = 'chat.' . $this->chatMessage->conversation_id;
        
        \Log::info('NewChatMessage Event: Broadcasting on channel', [
            'channel' => $channelName,
            'message_id' => $this->chatMessage->id,
            'conversation_id' => $this->chatMessage->conversation_id
        ]);
        
        // Use public channel for chat (accessible by both authenticated and unauthenticated users)
        return new Channel($channelName);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith() {
        return [
            'chatMessage' => [
                'id' => $this->chatMessage->id,
                'message' => $this->chatMessage->message,
                'conversation_id' => $this->chatMessage->conversation_id,
                'user_id' => $this->chatMessage->user_id,
                'contact_id' => $this->chatMessage->contact_id,
                'created_at' => $this->chatMessage->created_at,
                'updated_at' => $this->chatMessage->updated_at,
                'user' => $this->chatMessage->user ? [
                    'id' => $this->chatMessage->user->id,
                    'first_name' => $this->chatMessage->user->first_name,
                    'last_name' => $this->chatMessage->user->last_name,
                    'photo' => $this->chatMessage->user->photo_path,
                ] : null,
                'contact' => $this->chatMessage->contact ? [
                    'id' => $this->chatMessage->contact->id,
                    'first_name' => $this->chatMessage->contact->first_name,
                    'last_name' => $this->chatMessage->contact->last_name,
                    'email' => $this->chatMessage->contact->email,
                ] : null,
            ]
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs() {
        return 'NewChatMessage';
    }
}
