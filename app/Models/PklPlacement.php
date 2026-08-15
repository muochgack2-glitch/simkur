<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PklPlacement extends Model
{
    protected $fillable = [
        'academic_year_id', 'student_id', 'pkl_company_id',
        'start_date', 'end_date', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date'];
    }

    public function activity(): BelongsTo { return $this->belongsTo(AcademicYear::class, 'academic_year_id'); }
    public function student(): BelongsTo { return $this->belongsTo(User::class, 'student_id'); }
    public function company(): BelongsTo { return $this->belongsTo(PklCompany::class, 'pkl_company_id'); }
    public function moves(): HasMany { return $this->hasMany(PklPlacementMove::class)->orderByDesc('created_at'); }

    public function scopeActive($q) { return $q->where('status', 'active'); }
    public function scopeByActivity($q, $id) { return $q->where('academic_year_id', $id); }
}