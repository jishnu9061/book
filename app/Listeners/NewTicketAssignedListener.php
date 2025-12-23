<?php

namespace App\Listeners;

use App\Events\AssignedUser;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * New Ticket Assigned Listener
 * 
 * Handles ticket assignment notifications using the centralized notification service.
 */
class NewTicketAssignedListener
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(AssignedUser $event): void
    {
        try {
            // Handle both old and new data formats
            if (is_array($event->ticketId)) {
                $data = $event->ticketId;
                $ticketId = $data['ticket_id'] ?? null;
            } else {
                $ticketId = $event->ticketId;
            }
            
            // Validate event data
            if (!$ticketId || !is_numeric($ticketId)) {
                Log::warning('Invalid ticket ID received in NewTicketAssignedListener', [
                    'ticket_id' => $ticketId
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
            ])->find($ticketId);

            if (!$ticket) {
                Log::warning('Ticket not found in NewTicketAssignedListener', [
                    'ticket_id' => $ticketId
                ]);
                return;
            }

            if (!$ticket->assignedTo) {
                Log::warning('No assigned user found for ticket in NewTicketAssignedListener', [
                    'ticket_id' => $ticketId
                ]);
                return;
            }

            // Send notification using centralized service
            $this->notificationService->sendTicketAssignedNotification($ticket, $ticket->assignedTo);

        } catch (Exception $e) {
            Log::error('Error in NewTicketAssignedListener::handle', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ticket_id' => $event->ticketId ?? null
            ]);
        }
    }
}
