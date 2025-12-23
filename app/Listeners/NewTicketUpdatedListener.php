<?php

namespace App\Listeners;

use App\Events\TicketUpdated;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * New Ticket Updated Listener
 * 
 * Handles ticket update notifications using the centralized notification service.
 */
class NewTicketUpdatedListener
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(TicketUpdated $event): void
    {
        try {
            $data = $event->data;
            
            // Validate event data
            if (!$this->validateEventData($data)) {
                Log::warning('Invalid event data received in NewTicketUpdatedListener', [
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
                'department',
                'category'
            ])->find($data['ticket_id']);

            if (!$ticket) {
                Log::warning('Ticket not found in NewTicketUpdatedListener', [
                    'ticket_id' => $data['ticket_id']
                ]);
                return;
            }

            // Extract changes from event data
            $changes = $data['changes'] ?? [];

            // Send notification using centralized service
            $this->notificationService->sendTicketUpdatedNotification($ticket, $changes);

        } catch (Exception $e) {
            Log::error('Error in NewTicketUpdatedListener::handle', [
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
        return isset($data['ticket_id']) && is_numeric($data['ticket_id']);
    }
}
