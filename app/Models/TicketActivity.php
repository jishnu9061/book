<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'activity_type',
        'field_name',
        'old_value',
        'new_value',
        'description',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a new activity record
     */
    public static function createActivity($ticketId, $action, $description, $userId = null, $field = null, $oldValue = null, $newValue = null, $metadata = null)
    {
        return static::create([
            'ticket_id' => $ticketId,
            'user_id' => $userId,
            'activity_type' => $action,
            'field_name' => $field,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'description' => $description,
            'metadata' => $metadata
        ]);
    }

    /**
     * Log ticket creation
     */
    public static function logCreated(Ticket $ticket, $userId = null)
    {
        return static::createActivity(
            $ticket->id,
            'created',
            "Ticket #{$ticket->uid} was created",
            $userId
        );
    }

    /**
     * Log field changes
     */
    public static function logFieldChange(Ticket $ticket, $fieldName, $oldValue, $newValue, $userId = null)
    {
        $fieldLabels = [
            'status_id' => 'Status',
            'priority_id' => 'Priority',
            'assigned_to' => 'Assignment',
            'department_id' => 'Department',
            'category_id' => 'Category',
            'type_id' => 'Type',
            'subject' => 'Subject',
            'details' => 'Description',
            'due_date' => 'Due Date',
            'impact_level' => 'Impact Level',
            'urgency_level' => 'Urgency Level',
            'source' => 'Source',
            'tags' => 'Tags'
        ];

        $fieldLabel = $fieldLabels[$fieldName] ?? ucfirst(str_replace('_', ' ', $fieldName));
        
        $description = "{$fieldLabel} changed";
        if ($oldValue && $newValue) {
            $description .= " from '{$oldValue}' to '{$newValue}'";
        } elseif ($newValue) {
            $description .= " to '{$newValue}'";
        }

        return static::createActivity(
            $ticket->id,
            'field_changed',
            $description,
            $userId,
            $fieldName,
            $oldValue,
            $newValue
        );
    }

    /**
     * Log assignment changes
     */
    public static function logAssignment(Ticket $ticket, $oldAssigneeId, $newAssigneeId, $userId = null)
    {
        $oldAssignee = $oldAssigneeId ? User::find($oldAssigneeId) : null;
        $newAssignee = $newAssigneeId ? User::find($newAssigneeId) : null;

        // Use first_name and last_name if available, fallback to name
        $oldName = $oldAssignee ? ($oldAssignee->first_name && $oldAssignee->last_name ? 
            $oldAssignee->first_name . ' ' . $oldAssignee->last_name : $oldAssignee->name) : 'Unassigned';
        $newName = $newAssignee ? ($newAssignee->first_name && $newAssignee->last_name ? 
            $newAssignee->first_name . ' ' . $newAssignee->last_name : $newAssignee->name) : 'Unassigned';

        // Create more descriptive message
        if ($oldAssigneeId && $newAssigneeId) {
            $description = "Ticket reassigned from {$oldName} to {$newName}";
        } elseif ($newAssigneeId) {
            $description = "Ticket assigned to {$newName}";
        } elseif ($oldAssigneeId) {
            $description = "Ticket unassigned from {$oldName}";
        } else {
            $description = "Ticket assignment updated";
        }

        return static::createActivity(
            $ticket->id,
            'assigned',
            $description,
            $userId,
            'assigned_to',
            $oldAssigneeId,
            $newAssigneeId
        );
    }

    /**
     * Log status changes
     */
    public static function logStatusChange(Ticket $ticket, $oldStatusId, $newStatusId, $userId = null)
    {
        $oldStatus = $oldStatusId ? Status::find($oldStatusId) : null;
        $newStatus = $newStatusId ? Status::find($newStatusId) : null;

        $oldName = $oldStatus ? $oldStatus->name : 'None';
        $newName = $newStatus ? $newStatus->name : 'None';

        // Create more descriptive message based on status transition
        if ($newStatus && $newStatus->slug === 'closed') {
            $description = "Ticket closed";
        } elseif ($newStatus && $newStatus->slug === 'resolved') {
            $description = "Ticket resolved";
        } elseif ($oldStatus && $oldStatus->slug === 'closed' && $newStatus) {
            $description = "Ticket reopened - status changed to {$newName}";
        } else {
            $description = "Status changed from {$oldName} to {$newName}";
        }

        return static::createActivity(
            $ticket->id,
            'status_changed',
            $description,
            $userId,
            'status_id',
            $oldStatusId,
            $newStatusId
        );
    }

    /**
     * Log comment addition
     */
    public static function logComment(Ticket $ticket, $commentId, $userId = null)
    {
        return static::createActivity(
            $ticket->id,
            'commented',
            'A comment was added',
            $userId,
            'comment_id',
            null,
            $commentId
        );
    }

    /**
     * Log attachment addition
     */
    public static function logAttachment(Ticket $ticket, $attachmentId, $fileName, $userId = null)
    {
        return static::createActivity(
            $ticket->id,
            'attachment_added',
            "Attachment '{$fileName}' was added",
            $userId,
            'attachment_id',
            null,
            $attachmentId
        );
    }

    /**
     * Log SLA breach
     */
    public static function logSlaBreach(Ticket $ticket, $breachType = 'resolution')
    {
        $description = "SLA {$breachType} time breached";
        
        return static::createActivity(
            $ticket->id,
            'sla_breach',
            $description,
            null,
            'sla_breach',
            null,
            $breachType
        );
    }
}