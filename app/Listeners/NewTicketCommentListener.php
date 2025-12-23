<?php

namespace App\Listeners;

use App\Events\TicketNewComment;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * New Ticket Comment Listener
 * 
 * Handles ticket comment notifications using the centralized notification service.
 */
class NewTicketCommentListener
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(TicketNewComment $event): void
    {
        try {
            $data = $event->data;
            
            // Validate event data
            if (!$this->validateEventData($data)) {
                Log::warning('Invalid event data received in NewTicketCommentListener', [
                    'data' => $data
                ]);
                return;
            }

            // Get ticket with relationships
            $ticket = \App\Models\Ticket::with([
                'user', 
                'ticketType', 
                'assignedTo', 
                'priority', 
                'status', 
                'department'
            ])->find($data['ticket_id']);

            if (!$ticket) {
                Log::warning('Ticket not found in NewTicketCommentListener', [
                    'ticket_id' => $data['ticket_id']
                ]);
                return;
            }

            // Get commenter user
            $commenter = \App\Models\User::find($data['user_id'] ?? null);
            if (!$commenter) {
                Log::warning('Commenter not found in NewTicketCommentListener', [
                    'user_id' => $data['user_id'] ?? null
                ]);
                return;
            }

            $comment = $data['comment'] ?? '';

            // Send notification using centralized service
            $this->notificationService->sendTicketCommentNotification($ticket, $comment, $commenter);

        } catch (Exception $e) {
            Log::error('Error in NewTicketCommentListener::handle', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'event_data' => $event->data ?? null
            ]);
        }
    }

    /**
     * Validate event data structure
     */
    protected function validateEventData(array $data): bool
    {
        return isset($data['ticket_id']) && 
               is_numeric($data['ticket_id']) && 
               isset($data['user_id']) && 
               is_numeric($data['user_id']) &&
               isset($data['comment']);
    }
}
