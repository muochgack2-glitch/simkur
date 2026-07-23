<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get teachers that teach this subject
     */
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'teacher_subjects', 'subject_id', 'user_id')
            ->where('role', 'guru');
    }

    /**
     * Get teaching journals for this subject
     */
    public function teachingJournals()
    {
        return $this->hasMany(TeachingJournal::class, 'subject_id');
    }

    /**
     * Scope for active subjects only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get formatted status
     */
    public function getStatusLabel(): string
    {
        return $this->is_active ? 'Aktif' : 'Tidak Aktif';
    }

    /**
     * Get status badge color
     */
    public function getStatusColor(): string
    {
        return $this->is_active ? 'green' : 'red';
    }
}
