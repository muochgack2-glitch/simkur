<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PklPeriod extends Model
{
    protected $fillable = [
        'academic_year_id', 'period_number', 'title',
        'description', 'start_date', 'end_date', 'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function courses()
    {
        return $this->hasMany(PklCourse::class);
    }

    public function isCurrentPeriod(): bool
    {
        $now = Carbon::now();
        return $now->between($this->start_date, $this->end_date);
    }

    public function isPast(): bool
    {
        return Carbon::now()->gt($this->end_date);
    }

    public function isFuture(): bool
    {
        return Carbon::now()->lt($this->start_date);
    }

    public function getStatusLabel(): string
    {
        if ($this->isCurrentPeriod()) return 'Aktif';
        if ($this->isPast()) return 'Selesai';
        return 'Mendatang';
    }

    public function getDateRangeLabel(): string
    {
        return $this->start_date->translatedFormat('d M') . ' - ' . $this->end_date->translatedFormat('d M Y');
    }
}