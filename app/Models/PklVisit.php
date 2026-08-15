<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PklVisit extends Model
{
    protected $fillable = [
        'academic_year_id', 'teacher_id', 'pkl_company_id',
        'scheduled_date', 'actual_date', 'status',
        'notes', 'findings', 'recommendations', 'photo',
    ];

    protected function casts(): array
    {
        return ['scheduled_date' => 'date', 'actual_date' => 'date'];
    }

    public function academicYear(): BelongsTo { return $this->belongsTo(AcademicYear::class); }
    public function teacher(): BelongsTo { return $this->belongsTo(User::class, 'teacher_id'); }
    public function company(): BelongsTo { return $this->belongsTo(PklCompany::class, 'pkl_company_id'); }

    public function scopeScheduled($q) { return $q->where('status', 'scheduled'); }
    public function scopeCompleted($q) { return $q->where('status', 'completed'); }
}