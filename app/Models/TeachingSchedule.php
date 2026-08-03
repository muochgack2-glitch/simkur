<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeachingSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject_id',
        'academic_year_id',
        'day_of_week',
        'time_slot_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'time_slot_id' => 'array',
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

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeForDay($query, $dayName)
    {
        return $query->where('day_of_week', $dayName);
    }

    public function scopeForAcademicYear($query, $academicYearId = null)
    {
        if ($academicYearId === null) {
            $academicYearId = AcademicYear::where('is_active', true)->value('id');
        }
        return $query->where('academic_year_id', $academicYearId);
    }

    // Helper
    public function getScheduleLabel(): string
    {
        return sprintf(
            '%s - %s - %s (%s)',
            $this->schoolClass->name ?? '-',
            $this->subject->name ?? '-',
            $this->timeSlot->display_name ?? '-',
            $this->getDayLabel()
        );
    }

    public function getDayLabel(): string
    {
        // If already in Indonesian, return as-is
        if (in_array($this->day_of_week, ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'])) {
            return $this->day_of_week;
        }
        
        // Fallback for English day names (for backward compatibility)
        $days = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];
        
        return $days[$this->day_of_week] ?? $this->day_of_week;
    }

    /**
     * Get compact time slots display (for main view)
     * Example: "JP 6-9 (4 JP)" or "JP 2, 5, 8 (3 JP)"
     */
    public function getCompactTimeSlotsAttribute(): string
    {
        // If time_slot_id is array (new format)
        if (is_array($this->time_slot_id) && !empty($this->time_slot_id)) {
            $slots = $this->getTimeSlotsDetails();
            $count = count($slots);

            if ($count === 0) {
                return '-';
            }

            // Extract slot numbers
            $slotNumbers = array_map(function($slot) {
                return $this->extractSlotNumber($slot['name']);
            }, $slots);

            sort($slotNumbers);
            
            if ($count === 1) {
                return "JP {$slotNumbers[0]} (1 JP)";
            }

            // Check if consecutive
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

        // Fallback for old format (single time_slot_id)
        if ($this->timeSlot) {
            $number = $this->extractSlotNumber($this->timeSlot->name);
            return "JP {$number} (1 JP)";
        }

        return '-';
    }

    /**
     * Get detailed time slots for tooltip
     */
    public function getDetailedTimeSlotsAttribute(): string
    {
        if (is_array($this->time_slot_id) && !empty($this->time_slot_id)) {
            $slots = $this->getTimeSlotsDetails();
            return implode(', ', array_map(function($slot) {
                return $slot['name'] . ' (' . $slot['time_range'] . ')';
            }, $slots));
        }

        if ($this->timeSlot) {
            return $this->timeSlot->name . ' (' . $this->timeSlot->time_range . ')';
        }

        return '-';
    }

    /**
     * Get time slots details from IDs
     */
    public function getTimeSlotsDetails(): array
    {
        if (!is_array($this->time_slot_id) || empty($this->time_slot_id)) {
            return [];
        }

        return TimeSlot::whereIn('id', $this->time_slot_id)
            ->ordered()
            ->get()
            ->map(function($slot) {
                return [
                    'id' => $slot->id,
                    'name' => $slot->name,
                    'time_range' => $slot->time_range,
                ];
            })
            ->toArray();
    }

    /**
     * Extract slot number from time slot name
     * Example: "Jam ke-6" -> 6
     */
    private function extractSlotNumber($name): int
    {
        if (preg_match('/Jam ke-(\d+)/', $name, $matches)) {
            return (int)$matches[1];
        }
        if (preg_match('/JP[- ]?(\d+)/', $name, $matches)) {
            return (int)$matches[1];
        }
        return (int)$name;
    }
}
