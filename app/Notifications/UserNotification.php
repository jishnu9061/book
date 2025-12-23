<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

/**
 * User Notification
 * 
 * Handles notifications for user-related events.
 */
class UserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $user;
    public string $type;
    public array $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, string $type = 'created', array $data = [])
    {
        $this->user = $user;
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
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
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
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
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
            'created' => "New user account created: {$this->user->name}",
            'updated' => "User account updated: {$this->user->name}",
            'activated' => "User account activated: {$this->user->name}",
            'deactivated' => "User account deactivated: {$this->user->name}",
            'password_reset' => "Password reset requested for: {$this->user->name}",
            default => "User notification: {$this->user->name}",
        };
    }

    /**
     * Get the notification URL
     */
    protected function getUrl(): string
    {
        return match ($this->type) {
            'created', 'updated' => route('users.edit', $this->user->id),
            'password_reset' => route('password.reset'),
            default => route('dashboard'),
        };
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
