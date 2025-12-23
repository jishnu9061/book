<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

/**
 * Ticket Notification
 * 
 * Handles in-app and broadcast notifications for ticket-related events.
 */
class TicketNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Ticket $ticket;
    public string $type;
    public array $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, string $type = 'created', array $data = [])
    {
        $this->ticket = $ticket;
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        $channels = ['database'];

        // Add broadcast channel if Pusher is configured
        if ($this->isBroadcastingEnabled()) {
            $channels[] = 'broadcast';
        }

        return $channels;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_uid' => $this->ticket->uid,
            'ticket_subject' => $this->ticket->subject,
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
            'ticket_id' => $this->ticket->id,
            'ticket_uid' => $this->ticket->uid,
            'ticket_subject' => $this->ticket->subject,
            'type' => $this->type,
            'message' => $this->getMessage(),
            'url' => $this->getUrl(),
            'data' => $this->data,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get the notification message based on type
     */
    protected function getMessage(): string
    {
        return match ($this->type) {
            'created' => "A new ticket has been created: #{$this->ticket->uid}",
            'updated' => "Ticket #{$this->ticket->uid} has been updated",
            'assigned' => "You have been assigned to ticket #{$this->ticket->uid}",
            'commented' => "New comment added to ticket #{$this->ticket->uid}",
            'status_changed' => "Ticket #{$this->ticket->uid} status has been changed",
            'priority_changed' => "Ticket #{$this->ticket->uid} priority has been changed",
            default => "Ticket #{$this->ticket->uid} notification",
        };
    }

    /**
     * Get the notification URL
     */
    protected function getUrl(): string
    {
        return route('tickets.edit', $this->ticket->uid);
    }

    /**
     * Check if broadcasting is enabled
     */
    protected function isBroadcastingEnabled(): bool
    {
        $pusherKey = config('broadcasting.connections.pusher.key');
        $pusherSecret = config('broadcasting.connections.pusher.secret');
        $pusherAppId = config('broadcasting.connections.pusher.app_id');

        return !empty($pusherKey) && !empty($pusherSecret) && !empty($pusherAppId);
    }
}
