<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PklCompany extends Model
{
    protected $fillable = [
        'name', 'address', 'phone', 'contact_person', 'contact_phone',
        'capacity', 'business_field', 'suitable_departments', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return ['suitable_departments' => 'array'];
    }

    public function placements(): HasMany { return $this->hasMany(PklPlacement::class); }
    public function supervisors(): HasMany { return $this->hasMany(PklCompanySupervisor::class); }

    public function activePlacements($activityId = null)
    {
        $q = $this->placements()->where('status', 'active');
        if ($activityId) $q->where('pkl_activity_id', $activityId);
        return $q;
    }

    public function availableCapacity($activityId = null): int
    {
        return $this->capacity - $this->activePlacements($activityId)->count();
    }

    public function isFull($activityId = null): bool
    {
        return $this->availableCapacity($activityId) <= 0;
    }

    public function scopeActive($q) { return $q->where('status', 'active'); }
    public function scopeByDepartment($q, $dept) { return $q->whereJsonContains('suitable_departments', $dept); }
}