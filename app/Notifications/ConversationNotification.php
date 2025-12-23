<?php

namespace App\Notifications;

use App\Models\Conversation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

/**
 * Conversation Notification
 * 
 * Handles in-app and broadcast notifications for conversation-related events.
 */
class ConversationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Conversation $conversation;
    public string $type;
    public array $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(Conversation $conversation, string $type = 'new_message', array $data = [])
    {
        $this->conversation = $conversation;
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        $channels = ['database'];

        // Add broadcast channel if Pusher is configured and working
        if ($this->isBroadcastingEnabled()) {
            try {
                // Validate Pusher credentials before adding broadcast channel
                $pusherKey = config('broadcasting.connections.pusher.key');
                $pusherSecret = config('broadcasting.connections.pusher.secret');
                $pusherAppId = config('broadcasting.connections.pusher.app_id');
                
                if (!empty($pusherKey) && !empty($pusherSecret) && !empty($pusherAppId)) {
                    $channels[] = 'broadcast';
                }
            } catch (\Exception $e) {
                // If there's an error checking config, skip broadcast channel
                // Database notification will still work
                \Log::warning('Skipping broadcast channel due to configuration error', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $channels;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        return [
            'conversation_id' => $this->conversation->id,
            'conversation_type' => $this->conversation->type,
            'ticket_uid' => $this->conversation->ticket?->uid,
            'type' => $this->type,
            'message' => $this->getMessage(),
            'url' => $this->getUrl(),
            'data' => $this->data,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'conversation_id' => $this->conversation->id,
            'conversation_type' => $this->conversation->type,
            'ticket_uid' => $this->conversation->ticket?->uid,
            'type' => $this->type,
            'message' => $this->getMessage(),
            'url' => $this->getUrl(),
            'data' => $this->data,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get the notification message.
     */
    private function getMessage(): string
    {
        switch ($this->type) {
            case 'conversation_created':
                $ticketInfo = $this->conversation->ticket ? " for ticket #{$this->conversation->ticket->uid}" : '';
                return "You've been added to a new {$this->conversation->type} conversation{$ticketInfo}";
            
            case 'new_message':
                $senderName = $this->data['sender_name'] ?? 'Someone';
                $ticketInfo = $this->conversation->ticket ? " in ticket #{$this->conversation->ticket->uid}" : '';
                return "New message from {$senderName}{$ticketInfo}";
            
            case 'participant_added':
                $addedByName = $this->data['added_by_name'] ?? 'Someone';
                $ticketInfo = $this->conversation->ticket ? " for ticket #{$this->conversation->ticket->uid}" : '';
                return "You've been added to a conversation by {$addedByName}{$ticketInfo}";
            
            default:
                return 'New conversation activity';
        }
    }

    /**
     * Get the notification URL.
     */
    private function getUrl(): string
    {
        return route('conversations.view', $this->conversation->id);
    }

    /**
     * Check if broadcasting is enabled.
     */
    private function isBroadcastingEnabled(): bool
    {
        try {
            $defaultDriver = config('broadcasting.default');
            $pusherKey = config('broadcasting.connections.pusher.key');
            
            // Check if broadcasting is not set to 'null' and Pusher key exists
            return $defaultDriver !== 'null' && 
                   !empty($pusherKey) &&
                   $defaultDriver === 'pusher';
        } catch (\Exception $e) {
            \Log::warning('Error checking broadcasting configuration', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}





