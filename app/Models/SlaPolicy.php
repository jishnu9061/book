<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SlaPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'first_response_time',
        'resolution_time',
        'priority_conditions',
        'department_conditions',
        'category_conditions',
        'type_conditions',
        'business_hours',
        'holidays',
        'escalation_time',
        'escalation_actions'
    ];

    protected $casts = [
        'priority_conditions' => 'array',
        'department_conditions' => 'array',
        'category_conditions' => 'array',
        'type_conditions' => 'array',
        'business_hours' => 'array',
        'holidays' => 'array',
        'escalation_actions' => 'array',
        'is_active' => 'boolean'
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Check if this SLA policy applies to a given ticket
     */
    public function appliesTo(Ticket $ticket)
    {
        if (!$this->is_active) {
            return false;
        }

        // Check priority conditions
        if ($this->priority_conditions && !empty($this->priority_conditions)) {
            if (!in_array($ticket->priority_id, $this->priority_conditions)) {
                return false;
            }
        }

        // Check department conditions
        if ($this->department_conditions && !empty($this->department_conditions)) {
            if (!in_array($ticket->department_id, $this->department_conditions)) {
                return false;
            }
        }

        // Check category conditions
        if ($this->category_conditions && !empty($this->category_conditions)) {
            if (!in_array($ticket->category_id, $this->category_conditions)) {
                return false;
            }
        }

        // Check type conditions
        if ($this->type_conditions && !empty($this->type_conditions)) {
            if (!in_array($ticket->type_id, $this->type_conditions)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate SLA due date for a ticket
     */
    public function calculateDueDate(Ticket $ticket, $type = 'resolution')
    {
        $timeLimit = $type === 'first_response' ? $this->first_response_time : $this->resolution_time;
        
        if (!$timeLimit) {
            return null;
        }

        $startTime = $ticket->created_at;
        $dueDate = $this->addBusinessHours($startTime, $timeLimit);

        return $dueDate;
    }

    /**
     * Add business hours to a datetime, considering business hours and holidays
     */
    private function addBusinessHours(Carbon $startTime, $minutes)
    {
        $current = $startTime->copy();
        $remainingMinutes = $minutes;

        while ($remainingMinutes > 0) {
            // Check if current time is within business hours
            if ($this->isBusinessHour($current)) {
                $remainingMinutes--;
            }
            $current->addMinute();
        }

        return $current;
    }

    /**
     * Check if a given time is within business hours
     */
    private function isBusinessHour(Carbon $time)
    {
        // Check if it's a holiday
        if ($this->isHoliday($time)) {
            return false;
        }

        // Default business hours (9 AM to 5 PM, Monday to Friday)
        $businessHours = $this->business_hours ?? [
            'monday' => ['start' => '09:00', 'end' => '17:00'],
            'tuesday' => ['start' => '09:00', 'end' => '17:00'],
            'wednesday' => ['start' => '09:00', 'end' => '17:00'],
            'thursday' => ['start' => '09:00', 'end' => '17:00'],
            'friday' => ['start' => '09:00', 'end' => '17:00'],
        ];

        $dayName = strtolower($time->format('l'));
        
        if (!isset($businessHours[$dayName])) {
            return false;
        }

        $dayHours = $businessHours[$dayName];
        $currentTime = $time->format('H:i');

        return $currentTime >= $dayHours['start'] && $currentTime <= $dayHours['end'];
    }

    /**
     * Check if a given date is a holiday
     */
    private function isHoliday(Carbon $date)
    {
        $holidays = $this->holidays ?? [];
        
        foreach ($holidays as $holiday) {
            if (isset($holiday['date']) && $date->format('Y-m-d') === $holiday['date']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the appropriate SLA policy for a ticket
     */
    public static function getForTicket(Ticket $ticket)
    {
        return static::where('is_active', true)
            ->get()
            ->first(function ($policy) use ($ticket) {
                return $policy->appliesTo($ticket);
            });
    }
}