<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\HelpDesk;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid', 'subject', 'status_id', 'open', 'due', 'close', 'response',
        'user_id', 'contact_id', 'client_type', 'email', 'created_by',
        'location', 'priority_id', 'department_id', 'category_id',
        'sub_category_id', 'assigned_to', 'type_id', 'details', 'review_id',
        'due_date', 'estimated_hours', 'actual_hours', 'sla_breach_at',
        'resolution', 'tags', 'source', 'impact_level', 'urgency_level',
        'parent_ticket_id', 'template_id', 'last_customer_response',
        'last_agent_response', 'custom_fields', 'external_id', 'sla_policy_id'
    ];

    protected $casts = [
        'tags' => 'array',
        'custom_fields' => 'array',
        'due_date' => 'datetime',
        'sla_breach_at' => 'datetime',
        'last_customer_response' => 'datetime',
        'last_agent_response' => 'datetime',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
    ];

    protected static function booted()
    {

        static::created(function ($ticket) {
            if (!$ticket->uid) {
                $ticket->uid = (string) (100000 + $ticket->id);
                $ticket->saveQuietly();
            }
        });

        static::deleting(function ($ticket) {
            $ticket->comments()->delete();
            $ticket->attachments()->delete();
            $ticket->reviews()->delete();
            $ticket->ticketEntries()->delete();
        });
    }

    public function resolveRouteBinding($value, $field = null) {
        return $this->where($field ?? 'id', $value)->firstOrFail();
    }

    public function scopeOrderBySubject($query){
        $query->orderBy('subject');
    }

    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function priority(){
        return $this->belongsTo(Priority::class, 'priority_id');
    }

    public function review(){
        return $this->belongsTo(Review::class, 'review_id');
    }

    public function attachments(){
        return $this->hasMany(Attachment::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function ticketEntries(){
        return $this->hasMany(TicketEntry::class, 'ticket_id');
    }

    public function reviews(){
        return $this->hasMany(Review::class);
    }

    public function status(){
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function department(){
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function ticketType(){
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function contact(){
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(){
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function parent()
    {
        return $this->belongsTo(Ticket::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Ticket::class, 'parent_id');
    }

    public function subCategory(){
        return $this->belongsTo(Category::class, 'sub_category_id');
    }

    public function assignedTo(){
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function parentTicket(){
        return $this->belongsTo(Ticket::class, 'parent_ticket_id');
    }

    public function childTickets(){
        return $this->hasMany(Ticket::class, 'parent_ticket_id');
    }

    public function template(){
        return $this->belongsTo(TicketTemplate::class, 'template_id');
    }

    public function activities(){
        return $this->hasMany(TicketActivity::class);
    }

    public function conversations(){
        return $this->hasMany(Conversation::class);
    }

    /**
     * Get AI classifications for this ticket
     */
    public function aiClassifications()
    {
        return $this->hasMany(AITicketClassification::class);
    }

    /**
     * Get the latest AI classification for this ticket
     */
    public function latestAiClassification()
    {
        return $this->hasOne(AITicketClassification::class)->latest();
    }

    public function slaPolicy(){
        return $this->belongsTo(SlaPolicy::class, 'sla_policy_id');
    }

    public function getDueAttribute($date){
        return Carbon::parse($date)->format('Y-m-d');
    }

    // New helper methods for enhanced fields
    public function isOverdue(){
        return $this->due_date && $this->due_date->isPast() && !$this->isClosed();
    }

    public function isClosed(){
        return $this->status && $this->status->slug === 'closed';
    }

    public function isSlaBreached(){
        return $this->sla_breach_at && $this->sla_breach_at->isPast();
    }

    public function getSlaStatus(){
        if ($this->isSlaBreached()) {
            return 'breached';
        }
        if ($this->isOverdue()) {
            return 'overdue';
        }
        if ($this->due_date && $this->due_date->diffInHours(now()) <= 24) {
            return 'warning';
        }
        return 'normal';
    }

    public function getImpactLevelColor(){
        return match($this->impact_level) {
            'critical' => 'red',
            'high' => 'orange',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray'
        };
    }

    public function getUrgencyLevelColor(){
        return match($this->urgency_level) {
            'critical' => 'red',
            'high' => 'orange',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray'
        };
    }

    /**
     * Apply SLA policy to this ticket
     */
    public function applySlaPolicy()
    {
        // Check if SLA is already applied to prevent duplicates
        if ($this->due_date && $this->sla_policy_id) {
            return; // SLA already applied
        }

        $slaPolicy = SlaPolicy::getForTicket($this);
        
        if ($slaPolicy) {
            $dueDate = $slaPolicy->calculateDueDate($this, 'resolution');
            
            // Only update if due_date is different or not set
            if (!$this->due_date || $this->due_date->ne($dueDate)) {
                $this->update([
                    'due_date' => $dueDate,
                    'sla_policy_id' => $slaPolicy->id
                ]);
                
                // Log SLA application only once
                TicketActivity::createActivity(
                    $this->id,
                    'sla_applied',
                    "SLA policy '{$slaPolicy->name}' applied",
                    null,
                    'sla_policy_id',
                    null,
                    $slaPolicy->id
                );
            }
        }
    }

    /**
     * Check if ticket is approaching SLA breach
     */
    public function isApproachingSlaBreach($thresholdHours = 2)
    {
        if (!$this->due_date || $this->isClosed()) {
            return false;
        }

        $threshold = now()->addHours($thresholdHours);
        return $this->due_date <= $threshold && $this->due_date > now();
    }

    /**
     * Get time remaining until SLA breach
     */
    public function getSlaTimeRemaining()
    {
        if (!$this->due_date || $this->isClosed()) {
            return null;
        }

        return now()->diffInMinutes($this->due_date, false);
    }

    /**
     * Get SLA status with more detailed information
     */
    public function getDetailedSlaStatus()
    {
        if (!$this->due_date) {
            return [
                'status' => 'no_sla',
                'message' => 'No SLA policy applied',
                'color' => 'gray'
            ];
        }

        if ($this->isClosed()) {
            $isBreached = $this->due_date < $this->updated_at;
            return [
                'status' => $isBreached ? 'breached' : 'met',
                'message' => $isBreached ? 'SLA was breached' : 'SLA was met',
                'color' => $isBreached ? 'red' : 'green'
            ];
        }

        $timeRemaining = $this->getSlaTimeRemaining();
        
        if ($timeRemaining < 0) {
            return [
                'status' => 'breached',
                'message' => 'SLA breached',
                'color' => 'red',
                'time_remaining' => $timeRemaining
            ];
        }

        if ($timeRemaining <= 120) { // 2 hours
            return [
                'status' => 'warning',
                'message' => 'SLA approaching breach',
                'color' => 'orange',
                'time_remaining' => $timeRemaining
            ];
        }

        return [
            'status' => 'normal',
            'message' => 'SLA on track',
            'color' => 'green',
            'time_remaining' => $timeRemaining
        ];
    }

    public function scopeByCustomer($query, $id){
        if(!empty($id)){
            $query->where('user_id', $id);
        }
    }

    public function scopeByUser($query, $id){
        if(!empty($id)){
            $query->where('user_id', $id);
        }
    }

    public function scopeByAssign($query, $id){
        if(!empty($id)){
            $query->where('assigned_to', $id);
        }
    }

    public function scopeFilter($query, array $filters){
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $statusIds = Status::where('slug', 'like', '%'.$search.'%')->pluck('id');
            $priorityIds = Priority::where('name', 'like', '%'.$search.'%')->pluck('id');
            $assignedIds = User::where('first_name', 'like', '%'.$search.'%')->orWhere('last_name', 'like', '%'.$search.'%')->pluck('id');
            $query
                ->where('subject', 'like', '%'.$search.'%')
                ->orWhere('uid', 'like', '%'.$search.'%')
                ->orWhereIn('status_id', $statusIds)
                ->orWhereIn('priority_id', $priorityIds)
                ->orWhereIn('assigned_to', $assignedIds)
                ->orWhereIn('user_id', $assignedIds);
        })->when($filters['priority_id'] ?? null, function ($query, $priority) {
            $query->where('priority_id', $priority);
        })->when($filters['status_id'] ?? null, function ($query, $status) {
            $query->where('status_id', $status);
        })->when($filters['type_id'] ?? null, function ($query, $status) {
            $query->where('type_id', $status);
        })->when($filters['category_id'] ?? null, function ($query, $status) {
            $query->where('category_id', $status);
        })->when($filters['department_id'] ?? null, function ($query, $status) {
            $query->where('department_id', $status);
        });
    }
}
