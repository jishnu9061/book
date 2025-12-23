<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\AutoAssignmentRule;
use App\Models\SlaPolicy;
use Illuminate\Support\Facades\Auth;

class TicketWorkflowService
{
    /**
     * Process a new ticket creation
     */
    public static function processNewTicket(Ticket $ticket)
    {
        // Log ticket creation
        TicketActivity::logCreated($ticket, Auth::id());

        // Apply SLA policy only for new tickets
        if (!$ticket->due_date && !$ticket->sla_policy_id) {
            $ticket->applySlaPolicy();
        }

        // Auto-assign if no one is assigned
        if (!$ticket->assigned_to) {
            self::autoAssignTicket($ticket);
        }

        // Send notifications
        self::sendTicketCreatedNotifications($ticket);
    }

    /**
     * Process ticket updates
     */
    public static function processTicketUpdate(Ticket $ticket, array $changes, $userId = null)
    {
        foreach ($changes as $field => $change) {
            $oldValue = $change['old'];
            $newValue = $change['new'];

            // Handle specific field changes (these have their own specialized logging)
            switch ($field) {
                case 'assigned_to':
                    // Use specialized assignment logging instead of generic field change
                    self::handleAssignmentChange($ticket, $oldValue, $newValue, $userId);
                    break;
                case 'status_id':
                    // Use specialized status logging instead of generic field change
                    self::handleStatusChange($ticket, $oldValue, $newValue, $userId);
                    break;
                case 'priority_id':
                case 'department_id':
                case 'category_id':
                    // Log generic field change for these fields
                    TicketActivity::logFieldChange($ticket, $field, $oldValue, $newValue, $userId);
                    // Re-apply SLA policy only if these fields changed and no SLA is currently applied
                    if (!$ticket->sla_policy_id) {
                        $ticket->applySlaPolicy();
                    }
                    break;
                default:
                    // Log generic field change for all other fields
                    TicketActivity::logFieldChange($ticket, $field, $oldValue, $newValue, $userId);
                    break;
            }
        }

        // Check for SLA breaches
        self::checkSlaBreach($ticket);
    }

    /**
     * Handle assignment changes
     */
    private static function handleAssignmentChange(Ticket $ticket, $oldAssigneeId, $newAssigneeId, $userId = null)
    {
        TicketActivity::logAssignment($ticket, $oldAssigneeId, $newAssigneeId, $userId);

        // Send notification to new assignee
        if ($newAssigneeId) {
            self::sendAssignmentNotification($ticket, $newAssigneeId);
        }
    }

    /**
     * Handle status changes
     */
    private static function handleStatusChange(Ticket $ticket, $oldStatusId, $newStatusId, $userId = null)
    {
        TicketActivity::logStatusChange($ticket, $oldStatusId, $newStatusId, $userId);

        // If ticket is closed, update resolution time
        if ($newStatusId) {
            $newStatus = \App\Models\Status::find($newStatusId);
            if ($newStatus && $newStatus->slug === 'closed') {
                $ticket->update(['close' => now()]);
            }
        }
    }

    /**
     * Auto-assign a ticket
     */
    public static function autoAssignTicket(Ticket $ticket)
    {
        $assignedUser = AutoAssignmentRule::autoAssign($ticket);
        
        if ($assignedUser) {
            // Send notification to assigned user
            self::sendAssignmentNotification($ticket, $assignedUser->id);
            
            return $assignedUser;
        }

        return null;
    }

    /**
     * Check for SLA breaches
     */
    public static function checkSlaBreach(Ticket $ticket)
    {
        if ($ticket->isSlaBreached() && !$ticket->isClosed()) {
            // Log SLA breach
            TicketActivity::logSlaBreach($ticket);
            
            // Send breach notifications
            self::sendSlaBreachNotifications($ticket);
            
            // Update breach timestamp
            $ticket->update(['sla_breach_at' => now()]);
        }
    }

    /**
     * Send ticket created notifications
     */
    private static function sendTicketCreatedNotifications(Ticket $ticket)
    {
        try {
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationService->sendTicketCreatedNotification($ticket);
        } catch (\Exception $e) {
            \Log::error("Failed to send ticket created notifications", [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send assignment notifications
     */
    private static function sendAssignmentNotification(Ticket $ticket, $userId)
    {
        try {
            $assignedUser = \App\Models\User::find($userId);
            if ($assignedUser) {
                $notificationService = app(\App\Services\NotificationService::class);
                $notificationService->sendTicketAssignedNotification($ticket, $assignedUser);
            }
        } catch (\Exception $e) {
            \Log::error("Failed to send assignment notification", [
                'ticket_id' => $ticket->id,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send SLA breach notifications
     */
    private static function sendSlaBreachNotifications(Ticket $ticket)
    {
        try {
            // Get all admins and managers
            $adminRole = \App\Models\Role::where('slug', 'like', '%admin%')->first();
            $managerRole = \App\Models\Role::where('slug', 'like', '%manager%')->first();
            
            $recipients = collect();
            if ($adminRole) {
                $recipients = $recipients->merge(\App\Models\User::where('role_id', $adminRole->id)->get());
            }
            if ($managerRole) {
                $recipients = $recipients->merge(\App\Models\User::where('role_id', $managerRole->id)->get());
            }
            
            if ($recipients->isNotEmpty()) {
                $notificationService = app(\App\Services\NotificationService::class);
                $notificationService->sendCustomNotification(
                    $recipients,
                    'custom_mail',
                    [
                        'subject' => 'SLA Breach Alert',
                        'message' => "Ticket #{$ticket->uid} has breached its SLA deadline.",
                        'ticket_id' => $ticket->id,
                        'uid' => $ticket->uid,
                        'subject' => $ticket->subject,
                        'url' => config('app.url') . '/dashboard/tickets/' . $ticket->uid
                    ],
                    ['email', 'database', 'broadcast']
                );
            }
        } catch (\Exception $e) {
            \Log::error("Failed to send SLA breach notifications", [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get valid status transitions for a ticket
     */
    public static function getValidStatusTransitions(Ticket $ticket)
    {
        $currentStatus = $ticket->status;
        $allStatuses = \App\Models\Status::all();

        // Define transition rules (this could be configurable)
        $transitionRules = [
            'new' => ['in_progress', 'pending', 'closed'],
            'in_progress' => ['pending', 'resolved', 'closed'],
            'pending' => ['in_progress', 'closed'],
            'resolved' => ['closed', 'in_progress'],
            'closed' => [] // No transitions from closed
        ];

        $currentSlug = $currentStatus ? $currentStatus->slug : 'new';
        $allowedSlugs = $transitionRules[$currentSlug] ?? [];

        return $allStatuses->filter(function ($status) use ($allowedSlugs) {
            return in_array($status->slug, $allowedSlugs);
        });
    }

    /**
     * Validate status transition
     */
    public static function validateStatusTransition(Ticket $ticket, $newStatusId)
    {
        $validTransitions = self::getValidStatusTransitions($ticket);
        $newStatus = \App\Models\Status::find($newStatusId);

        if (!$newStatus) {
            return false;
        }

        return $validTransitions->contains('id', $newStatusId);
    }

    /**
     * Get ticket workflow statistics
     */
    public static function getWorkflowStats($userId = null, $dateRange = null)
    {
        $query = Ticket::query();

        if ($userId) {
            $query->where('assigned_to', $userId);
        }

        if ($dateRange) {
            $query->whereBetween('created_at', $dateRange);
        }

        $tickets = $query->get();

        return [
            'total_tickets' => $tickets->count(),
            'open_tickets' => $tickets->where('status.slug', '!=', 'closed')->count(),
            'closed_tickets' => $tickets->where('status.slug', 'closed')->count(),
            'sla_breached' => $tickets->filter(function ($ticket) {
                return $ticket->isSlaBreached();
            })->count(),
            'avg_resolution_time' => self::calculateAverageResolutionTime($tickets),
            'sla_compliance_rate' => self::calculateSlaComplianceRate($tickets)
        ];
    }

    /**
     * Calculate average resolution time
     */
    private static function calculateAverageResolutionTime($tickets)
    {
        $closedTickets = $tickets->filter(function ($ticket) {
            return $ticket->status && $ticket->status->slug === 'closed' && $ticket->close;
        });

        if ($closedTickets->isEmpty()) {
            return 0;
        }

        $totalMinutes = $closedTickets->sum(function ($ticket) {
            return $ticket->created_at->diffInMinutes($ticket->close);
        });

        return round($totalMinutes / $closedTickets->count(), 2);
    }

    /**
     * Calculate SLA compliance rate
     */
    private static function calculateSlaComplianceRate($tickets)
    {
        $ticketsWithSla = $tickets->filter(function ($ticket) {
            return $ticket->due_date;
        });

        if ($ticketsWithSla->isEmpty()) {
            return 100;
        }

        $compliantTickets = $ticketsWithSla->filter(function ($ticket) {
            if ($ticket->isClosed()) {
                return $ticket->close <= $ticket->due_date;
            }
            return !$ticket->isSlaBreached();
        });

        return round(($compliantTickets->count() / $ticketsWithSla->count()) * 100, 2);
    }
}
