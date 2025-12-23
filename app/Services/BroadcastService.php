<?php

namespace App\Services;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Broadcast;
use Exception;

/**
 * Broadcast Service
 * 
 * Handles real-time broadcasting of notifications using Laravel Echo and Pusher.
 */
class BroadcastService
{
    /**
     * Send ticket notification via broadcast
     */
    public function sendTicketNotification(User $user, Ticket $ticket): void
    {
        try {
            if (!$this->isBroadcastingEnabled()) {
                return;
            }

            $notificationData = [
                'ticket_id' => $ticket->id,
                'ticket_uid' => $ticket->uid,
                'ticket_subject' => $ticket->subject,
                'message' => "A new ticket has been created: #{$ticket->uid}",
                'url' => route('tickets.edit', $ticket->uid),
                'type' => 'ticket_created',
                'timestamp' => now()->toISOString(),
            ];

            // Send to user's private channel
            Broadcast::toUser($user)->event('ticket.notification', $notificationData);

            Log::info('Broadcast notification sent', [
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'channel' => 'App.Models.User.' . $user->id
            ]);

        } catch (Exception $e) {
            Log::error('Failed to send broadcast notification', [
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send ticket update notification
     */
    public function sendTicketUpdateNotification(User $user, Ticket $ticket, array $changes = []): void
    {
        try {
            if (!$this->isBroadcastingEnabled()) {
                return;
            }

            $notificationData = [
                'ticket_id' => $ticket->id,
                'ticket_uid' => $ticket->uid,
                'ticket_subject' => $ticket->subject,
                'message' => "Ticket #{$ticket->uid} has been updated",
                'url' => route('tickets.edit', $ticket->uid),
                'type' => 'ticket_updated',
                'changes' => $changes,
                'timestamp' => now()->toISOString(),
            ];

            Broadcast::toUser($user)->event('ticket.update', $notificationData);

            Log::info('Broadcast ticket update sent', [
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'changes' => array_keys($changes)
            ]);

        } catch (Exception $e) {
            Log::error('Failed to send broadcast ticket update', [
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send ticket assignment notification
     */
    public function sendTicketAssignmentNotification(User $user, Ticket $ticket): void
    {
        try {
            if (!$this->isBroadcastingEnabled()) {
                return;
            }

            $notificationData = [
                'ticket_id' => $ticket->id,
                'ticket_uid' => $ticket->uid,
                'ticket_subject' => $ticket->subject,
                'message' => "You have been assigned to ticket #{$ticket->uid}",
                'url' => route('tickets.edit', $ticket->uid),
                'type' => 'ticket_assigned',
                'timestamp' => now()->toISOString(),
            ];

            Broadcast::toUser($user)->event('ticket.assigned', $notificationData);

            Log::info('Broadcast ticket assignment sent', [
                'user_id' => $user->id,
                'ticket_id' => $ticket->id
            ]);

        } catch (Exception $e) {
            Log::error('Failed to send broadcast ticket assignment', [
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send ticket comment notification
     */
    public function sendTicketCommentNotification(User $user, Ticket $ticket, string $comment): void
    {
        try {
            if (!$this->isBroadcastingEnabled()) {
                return;
            }

            $notificationData = [
                'ticket_id' => $ticket->id,
                'ticket_uid' => $ticket->uid,
                'ticket_subject' => $ticket->subject,
                'message' => "New comment added to ticket #{$ticket->uid}",
                'url' => route('tickets.edit', $ticket->uid),
                'type' => 'ticket_comment',
                'comment' => $comment,
                'timestamp' => now()->toISOString(),
            ];

            Broadcast::toUser($user)->event('ticket.comment', $notificationData);

            Log::info('Broadcast ticket comment sent', [
                'user_id' => $user->id,
                'ticket_id' => $ticket->id
            ]);

        } catch (Exception $e) {
            Log::error('Failed to send broadcast ticket comment', [
                'user_id' => $user->id,
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send system-wide announcement
     */
    public function sendSystemAnnouncement(string $message, array $data = []): void
    {
        try {
            if (!$this->isBroadcastingEnabled()) {
                return;
            }

            $notificationData = array_merge([
                'message' => $message,
                'type' => 'system_announcement',
                'timestamp' => now()->toISOString(),
            ], $data);

            // Send to all authenticated users
            Broadcast::event('system.announcement', $notificationData);

            Log::info('System announcement broadcast sent', [
                'message' => $message,
                'data' => $data
            ]);

        } catch (Exception $e) {
            Log::error('Failed to send system announcement', [
                'message' => $message,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send notification to multiple users
     */
    public function sendBulkNotification(iterable $users, string $event, array $data): void
    {
        $successCount = 0;
        $failureCount = 0;

        foreach ($users as $user) {
            try {
                Broadcast::toUser($user)->event($event, $data);
                $successCount++;
            } catch (Exception $e) {
                $failureCount++;
                Log::error('Bulk broadcast failed for user', [
                    'user_id' => $user->id,
                    'event' => $event,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('Bulk broadcast completed', [
            'event' => $event,
            'success_count' => $successCount,
            'failure_count' => $failureCount,
            'total_count' => $successCount + $failureCount
        ]);
    }

    /**
     * Check if broadcasting is enabled and configured
     */
    public function isBroadcastingEnabled(): bool
    {
        try {
            $pusherKey = config('broadcasting.connections.pusher.key');
            $pusherSecret = config('broadcasting.connections.pusher.secret');
            $pusherAppId = config('broadcasting.connections.pusher.app_id');

            return !empty($pusherKey) && !empty($pusherSecret) && !empty($pusherAppId);
        } catch (Exception $e) {
            Log::error('Failed to check broadcasting configuration', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get broadcasting configuration status
     */
    public function getBroadcastingStatus(): array
    {
        try {
            $config = [
                'driver' => config('broadcasting.default'),
                'pusher_key' => config('broadcasting.connections.pusher.key'),
                'pusher_secret' => config('broadcasting.connections.pusher.secret') ? '***configured***' : null,
                'pusher_app_id' => config('broadcasting.connections.pusher.app_id'),
                'pusher_cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'pusher_use_tls' => config('broadcasting.connections.pusher.options.useTLS'),
            ];

            $issues = [];
            
            if (empty($config['pusher_key'])) {
                $issues[] = 'Pusher key is not configured';
            }
            
            if (empty($config['pusher_secret'])) {
                $issues[] = 'Pusher secret is not configured';
            }
            
            if (empty($config['pusher_app_id'])) {
                $issues[] = 'Pusher app ID is not configured';
            }

            return [
                'enabled' => $this->isBroadcastingEnabled(),
                'config' => $config,
                'issues' => $issues
            ];

        } catch (Exception $e) {
            Log::error('Failed to get broadcasting status', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'enabled' => false,
                'config' => [],
                'issues' => ['Failed to read configuration: ' . $e->getMessage()]
            ];
        }
    }

    /**
     * Test broadcasting connection
     */
    public function testBroadcastingConnection(): array
    {
        try {
            if (!$this->isBroadcastingEnabled()) {
                return [
                    'success' => false,
                    'message' => 'Broadcasting is not properly configured'
                ];
            }

            // Send a test event
            $testData = [
                'message' => 'Test broadcast message',
                'type' => 'test',
                'timestamp' => now()->toISOString(),
            ];

            Broadcast::event('test.broadcast', $testData);

            return [
                'success' => true,
                'message' => 'Broadcasting test successful'
            ];

        } catch (Exception $e) {
            Log::error('Broadcasting test failed', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Broadcasting test failed: ' . $e->getMessage()
            ];
        }
    }
}
