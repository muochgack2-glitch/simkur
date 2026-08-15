<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PklCompanySupervisor extends Model
{
    protected $fillable = ['academic_year_id', 'teacher_id', 'pkl_company_id'];

    public function activity(): BelongsTo { return $this->belongsTo(AcademicYear::class, 'academic_year_id'); }
    public function teacher(): BelongsTo { return $this->belongsTo(User::class, 'teacher_id'); }
    public function company(): BelongsTo { return $this->belongsTo(PklCompany::class, 'pkl_company_id'); }
}