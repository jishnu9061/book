<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AITicketClassification extends Model
{
    use HasFactory;

    protected $table = 'ai_ticket_classifications';

    protected $fillable = [
        'ticket_id',
        'priority_id',
        'category_id',
        'department_id',
        'type_id',
        'confidence_score',
        'reasoning',
        'ai_generated',
        'classification_data',
        'applied',
        'applied_at'
    ];

    protected $casts = [
        'confidence_score' => 'decimal:2',
        'ai_generated' => 'boolean',
        'applied' => 'boolean',
        'classification_data' => 'array',
        'applied_at' => 'datetime'
    ];

    /**
     * Get the ticket that owns the classification
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the priority that owns the classification
     */
    public function priority(): BelongsTo
    {
        return $this->belongsTo(Priority::class);
    }

    /**
     * Get the category that owns the classification
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the department that owns the classification
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the type that owns the classification
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    /**
     * Scope for high confidence classifications
     */
    public function scopeHighConfidence($query, $threshold = 0.8)
    {
        return $query->where('confidence_score', '>=', $threshold);
    }

    /**
     * Scope for applied classifications
     */
    public function scopeApplied($query)
    {
        return $query->where('applied', true);
    }

    /**
     * Scope for AI generated classifications
     */
    public function scopeAiGenerated($query)
    {
        return $query->where('ai_generated', true);
    }

    /**
     * Get confidence level as string
     */
    public function getConfidenceLevelAttribute(): string
    {
        if ($this->confidence_score >= 0.9) {
            return 'Very High';
        } elseif ($this->confidence_score >= 0.8) {
            return 'High';
        } elseif ($this->confidence_score >= 0.7) {
            return 'Medium';
        } elseif ($this->confidence_score >= 0.5) {
            return 'Low';
        } else {
            return 'Very Low';
        }
    }

    /**
     * Get confidence color for UI
     */
    public function getConfidenceColorAttribute(): string
    {
        if ($this->confidence_score >= 0.8) {
            return 'green';
        } elseif ($this->confidence_score >= 0.6) {
            return 'yellow';
        } else {
            return 'red';
        }
    }

    /**
     * Mark classification as applied
     */
    public function markAsApplied(): bool
    {
        return $this->update([
            'applied' => true,
            'applied_at' => now()
        ]);
    }
}
