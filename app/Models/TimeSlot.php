<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'order',
        'day_of_week',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active time slots
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered by order field
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Scope for filtering by day of week
     * Returns time slots for specific day OR time slots for all days
     */
    public function scopeForDay($query, $dayName)
    {
        return $query->where(function($q) use ($dayName) {
            $q->where('day_of_week', $dayName)
              ->orWhere('day_of_week', 'all')
              ->orWhereNull('day_of_week');
        });
    }

    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute(): string
    {
        return date('H:i', strtotime($this->start_time)) . ' - ' . date('H:i', strtotime($this->end_time));
    }

    /**
     * Get display name with time
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name . ' (' . $this->time_range . ')';
    }
}
