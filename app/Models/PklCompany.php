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

    public function activePlacements($academicYearId = null)
    {
        $q = $this->placements()->where('status', 'active');
        if ($academicYearId) $q->where('academic_year_id', $academicYearId);
        return $q;
    }

    public function availableCapacity($academicYearId = null): int
    {
        return $this->capacity - $this->activePlacements($academicYearId)->count();
    }

    public function isFull($academicYearId = null): bool
    {
        return $this->availableCapacity($academicYearId) <= 0;
    }

    public function scopeActive($q) { return $q->where('status', 'active'); }
    public function scopeByDepartment($q, $dept) { return $q->whereJsonContains('suitable_departments', $dept); }
}