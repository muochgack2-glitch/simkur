<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassPromotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_academic_year_id',
        'to_academic_year_id',
        'processed_by',
        'total_promoted',
        'total_graduated',
        'promotion_summary',
        'student_details',
        'notes',
        'is_rolled_back',
        'rolled_back_at',
        'rolled_back_by',
        'processed_at',
    ];

    protected $casts = [
        'promotion_summary' => 'array',
        'student_details' => 'array',
        'is_rolled_back' => 'boolean',
        'processed_at' => 'datetime',
        'rolled_back_at' => 'datetime',
    ];

    /**
     * Get the source academic year
     */
    public function fromAcademicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'from_academic_year_id');
    }

    /**
     * Get the target academic year
     */
    public function toAcademicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'to_academic_year_id');
    }

    /**
     * Get the user who processed this promotion
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get the user who rolled back this promotion
     */
    public function rolledBackBy()
    {
        return $this->belongsTo(User::class, 'rolled_back_by');
    }

    /**
     * Check if this promotion can be rolled back
     */
    public function canRollback(): bool
    {
        // Can't rollback if already rolled back
        if ($this->is_rolled_back) {
            return false;
        }

        // Can't rollback if no student details (old promotions before tracking feature)
        if (empty($this->student_details)) {
            return false;
        }

        // Can only rollback the most recent NON-ROLLED-BACK promotion for safety
        $latestActivePromotion = self::where('is_rolled_back', false)
            ->orderBy('processed_at', 'desc')
            ->first();
        
        return $this->id === $latestActivePromotion?->id;
    }
}
