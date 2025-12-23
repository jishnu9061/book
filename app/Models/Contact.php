<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'organization_id',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code'
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'id', $value)->firstOrFail();
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    /**
     * Get the creator of this contact (for backward compatibility)
     * This might be needed by the existing ChatController
     * Since contacts don't have a created_by field, we'll return a dummy relationship
     */
    public function creator()
    {
        // Return a relationship that always returns null
        // This prevents the eager loading error while maintaining compatibility
        return $this->belongsTo(User::class, 'created_by')->whereRaw('1 = 0');
    }

    public function getNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function scopeOrderByName($query)
    {
        $query->orderBy('last_name')->orderBy('first_name');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('first_name', 'like', '%'.$search.'%')
                    ->orWhere('last_name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhereHas('organization', function ($query) use ($search) {
                        $query->where('name', 'like', '%'.$search.'%');
                    });
            });
        });
    }
}
