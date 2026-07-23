<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAttendance extends Model
{
    protected $fillable = [
        'teaching_journal_id',
        'student_id',
        'status',
        'notes',
    ];

    // Relationships
    public function teachingJournal(): BelongsTo
    {
        return $this->belongsTo(TeachingJournal::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Helper methods
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'hadir' => 'Hadir',
            'sakit' => 'Sakit',
            'izin' => 'Izin',
            'alpha' => 'Alpha',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'hadir' => 'green',
            'sakit' => 'yellow',
            'izin' => 'blue',
            'alpha' => 'red',
            default => 'gray',
        };
    }
}
