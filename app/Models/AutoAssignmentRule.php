<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoAssignmentRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'priority',
        'conditions',
        'assignment_type',
        'assigned_user_id',
        'assigned_department_id',
        'assignment_config',
        'max_tickets_per_user',
        'consider_workload',
        'consider_skills'
    ];

    protected $casts = [
        'conditions' => 'array',
        'assignment_config' => 'array',
        'is_active' => 'boolean',
        'consider_workload' => 'boolean',
        'consider_skills' => 'boolean'
    ];

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function assignedDepartment()
    {
        return $this->belongsTo(Department::class, 'assigned_department_id');
    }

    /**
     * Check if this rule applies to a given ticket
     */
    public function appliesTo(Ticket $ticket)
    {
        if (!$this->is_active) {
            return false;
        }

        $conditions = $this->conditions ?? [];

        foreach ($conditions as $field => $expectedValues) {
            $ticketValue = $ticket->{$field};
            
            if (is_array($expectedValues)) {
                if (!in_array($ticketValue, $expectedValues)) {
                    return false;
                }
            } else {
                if ($ticketValue != $expectedValues) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get the user to assign based on this rule
     */
    public function getAssignedUser(Ticket $ticket)
    {
        switch ($this->assignment_type) {
            case 'user':
                return $this->assignedUser;
                
            case 'department':
                return $this->getUserFromDepartment($ticket);
                
            case 'round_robin':
                return $this->getRoundRobinUser($ticket);
                
            case 'workload_based':
                return $this->getWorkloadBasedUser($ticket);
                
            default:
                return null;
        }
    }

    /**
     * Get a user from the assigned department
     */
    private function getUserFromDepartment(Ticket $ticket)
    {
        if (!$this->assigned_department_id) {
            return null;
        }

        $department = Department::find($this->assigned_department_id);
        if (!$department) {
            return null;
        }

        // Get users in this department
        $users = User::whereHas('role', function($query) {
            $query->whereIn('slug', ['agent', 'manager', 'admin']);
        })->get();

        if ($this->consider_workload) {
            return $this->getLeastBusyUser($users);
        }

        return $users->first();
    }

    /**
     * Get user using round-robin assignment
     */
    private function getRoundRobinUser(Ticket $ticket)
    {
        $config = $this->assignment_config ?? [];
        $userIds = $config['user_ids'] ?? [];

        if (empty($userIds)) {
            return null;
        }

        // Get the last assigned user for this rule
        $lastAssigned = Ticket::where('assigned_to', '!=', null)
            ->whereIn('assigned_to', $userIds)
            ->orderBy('updated_at', 'desc')
            ->first();

        if (!$lastAssigned) {
            return User::find($userIds[0]);
        }

        $currentIndex = array_search($lastAssigned->assigned_to, $userIds);
        $nextIndex = ($currentIndex + 1) % count($userIds);

        return User::find($userIds[$nextIndex]);
    }

    /**
     * Get user based on workload
     */
    private function getWorkloadBasedUser(Ticket $ticket)
    {
        $config = $this->assignment_config ?? [];
        $userIds = $config['user_ids'] ?? [];

        if (empty($userIds)) {
            // Get all available agents
            $users = User::whereHas('role', function($query) {
                $query->whereIn('slug', ['agent', 'manager', 'admin']);
            })->get();
        } else {
            $users = User::whereIn('id', $userIds)->get();
        }

        return $this->getLeastBusyUser($users);
    }

    /**
     * Get the least busy user from a collection
     */
    private function getLeastBusyUser($users)
    {
        $userWorkloads = [];

        foreach ($users as $user) {
            $activeTickets = Ticket::where('assigned_to', $user->id)
                ->whereHas('status', function($query) {
                    $query->where('slug', '!=', 'closed');
                })
                ->count();

            $userWorkloads[$user->id] = $activeTickets;
        }

        if (empty($userWorkloads)) {
            return $users->first();
        }

        $leastBusyUserId = array_keys($userWorkloads, min($userWorkloads))[0];
        return $users->find($leastBusyUserId);
    }

    /**
     * Auto-assign a ticket using all applicable rules
     */
    public static function autoAssign(Ticket $ticket)
    {
        $rules = static::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();

        foreach ($rules as $rule) {
            if ($rule->appliesTo($ticket)) {
                $assignedUser = $rule->getAssignedUser($ticket);
                
                if ($assignedUser) {
                    // Check max tickets per user limit
                    if ($rule->max_tickets_per_user) {
                        $userTicketCount = Ticket::where('assigned_to', $assignedUser->id)
                            ->whereHas('status', function($query) {
                                $query->where('slug', '!=', 'closed');
                            })
                            ->count();

                        if ($userTicketCount >= $rule->max_tickets_per_user) {
                            continue; // Skip this rule, try next one
                        }
                    }

                    // Assign the ticket
                    $ticket->update(['assigned_to' => $assignedUser->id]);
                    
                    // Log the assignment
                    TicketActivity::logAssignment($ticket, null, $assignedUser->id);
                    
                    return $assignedUser;
                }
            }
        }

        return null;
    }
}