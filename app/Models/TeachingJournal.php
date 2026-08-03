<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeachingJournal extends Model
{
    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject_id',
        'academic_year_id',
        'date',
        'time_slot',
        'learning_objective',
        'topic',
        'teaching_method',
        'notes',
        'activity_photo',
        'total_students',
        'present_count',
        'sick_count',
        'permission_count',
        'absent_count',
    ];

    protected $casts = [
        'date' => 'date',
        'time_slot' => 'array', // Cast to array for JSON storage
        'total_students' => 'integer',
        'present_count' => 'integer',
        'sick_count' => 'integer',
        'permission_count' => 'integer',
        'absent_count' => 'integer',
    ];

    // Relationships
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(StudentAttendance::class);
    }

    // Helper method
    public function updateAttendanceStats(): void
    {
        $this->total_students = $this->attendances()->count();
        $this->present_count = $this->attendances()->where('status', 'hadir')->count();
        $this->sick_count = $this->attendances()->where('status', 'sakit')->count();
        $this->permission_count = $this->attendances()->where('status', 'izin')->count();
        $this->absent_count = $this->attendances()->where('status', 'alpha')->count();
        $this->save();
    }

    /**
     * Get formatted time slots display
     */
    public function getFormattedTimeSlotsAttribute(): string
    {
        if (empty($this->time_slot)) {
            return '-';
        }

        if (is_array($this->time_slot)) {
            return implode(', ', $this->time_slot);
        }

        return $this->time_slot;
    }

    /**
     * Get compact time slots display (for main view)
     * Example: "JP 6-9 (4 JP)" or "JP 2, 5, 8 (3 JP)"
     */
    public function getCompactTimeSlotsAttribute(): string
    {
        if (empty($this->time_slot) || !is_array($this->time_slot)) {
            return $this->time_slot ?? '-';
        }

        $slots = $this->time_slot;
        $count = count($slots);

        if ($count === 0) {
            return '-';
        }

        // Extract slot number from first slot
        $firstSlotNumber = $this->extractSlotNumber($slots[0]);

        if ($count === 1) {
            return "JP {$firstSlotNumber} (1 JP)";
        }

        // Check if consecutive
        $slotNumbers = array_map([$this, 'extractSlotNumber'], $slots);

        sort($slotNumbers);
        
        $isConsecutive = true;
        for ($i = 1; $i < count($slotNumbers); $i++) {
            if ($slotNumbers[$i] !== $slotNumbers[$i-1] + 1) {
                $isConsecutive = false;
                break;
            }
        }

        if ($isConsecutive) {
            return "JP {$slotNumbers[0]}-{$slotNumbers[count($slotNumbers)-1]} ({$count} JP)";
        } else {
            $numbers = implode(', ', $slotNumbers);
            return "JP {$numbers} ({$count} JP)";
        }
    }

    /**
     * Extract slot number from time slot string
     * Example: "Jam ke-6 (11:05 - 11:45)" -> 6
     */
    private function extractSlotNumber($slot): int
    {
        // Extract number from "Jam ke-6 (11:05 - 11:45)" format
        if (preg_match('/Jam ke-(\d+)/', $slot, $matches)) {
            return (int)$matches[1];
        }
        // Or just plain number
        return (int)$slot;
    }

    /**
     * Get detailed time slots for tooltip
     * Example: "Jam ke-6 (11:05-11:45), Jam ke-7 (11:45-12:25)"
     */
    public function getDetailedTimeSlotsAttribute(): string
    {
        if (empty($this->time_slot) || !is_array($this->time_slot)) {
            return $this->time_slot ?? '-';
        }

        return implode(', ', $this->time_slot);
    }

    /**
     * Get full URL for activity photo
     */
    public function getActivityPhotoUrlAttribute(): ?string
    {
        if (!$this->activity_photo) {
            return null;
        }

        return asset('storage/' . $this->activity_photo);
    }

    /**
     * Check if journal has photo
     */
    public function hasPhoto(): bool
    {
        return !empty($this->activity_photo);
    }
}
