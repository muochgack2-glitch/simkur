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
        'notes',
        'processed_at',
    ];

    protected $casts = [
        'promotion_summary' => 'array',
        'processed_at' => 'datetime',
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
}
