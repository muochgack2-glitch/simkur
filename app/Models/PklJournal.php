<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PklJournal extends Model
{
    protected $fillable = [
        'pkl_placement_id', 'student_id', 'journal_date', 'activities',
        'learnings', 'challenges', 'photo', 'status', 'supervisor_notes',
        'approved_by', 'approved_at',
    ];

    protected function casts(): array
    {
        return ['journal_date' => 'date', 'approved_at' => 'datetime'];
    }

    public function placement(): BelongsTo { return $this->belongsTo(PklPlacement::class, 'pkl_placement_id'); }
    public function student(): BelongsTo { return $this->belongsTo(User::class, 'student_id'); }
    public function approvedBy(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }

    public function scopeSubmitted($q) { return $q->where('status', 'submitted'); }
    public function scopeApproved($q) { return $q->where('status', 'approved'); }
}