<?php

namespace App\Listeners;

use App\Events\TicketCreated;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * New Ticket Created Listener
 * 
 * Handles ticket creation notifications using the centralized notification service.
 */
class NewTicketCreatedListener
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(TicketCreated $event): void
    {
        try {
            $data = $event->data;
            
            // Validate event data
            if (!$this->validateEventData($data)) {
                Log::warning('Invalid event data received in NewTicketCreatedListener', [
                    'data' => $data
                ]);
                return;
            }

            // Get ticket with relationships
            $ticket = \App\Models\Ticket::with(['user', 'ticketType', 'assignedTo', 'priority', 'status', 'department'])
                ->find($data['ticket_id']);

            if (!$ticket) {
                Log::warning('Ticket not found in NewTicketCreatedListener', [
                    'ticket_id' => $data['ticket_id']
                ]);
                return;
            }

            // Send notification using centralized service
            $this->notificationService->sendTicketCreatedNotification($ticket, $data);

        } catch (Exception $e) {
            Log::error('Error in NewTicketCreatedListener::handle', [
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
