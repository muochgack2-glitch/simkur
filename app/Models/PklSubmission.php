<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PklSubmission extends Model
{
    protected $fillable = [
        'pkl_assignment_id', 'student_id', 'content', 'file_path',
        'file_name', 'score', 'feedback', 'submitted_at', 'graded_at', 'is_late',
        'revision_requested', 'revision_note', 'revision_requested_at',
    ];

    protected function casts(): array
    {
        return [
            'score'                  => 'decimal:2',
            'submitted_at'           => 'datetime',
            'graded_at'              => 'datetime',
            'revision_requested_at'  => 'datetime',
            'is_late'                => 'boolean',
            'revision_requested'     => 'boolean',
        ];
    }

    public function assignment(): BelongsTo { return $this->belongsTo(PklAssignment::class, 'pkl_assignment_id'); }
    public function student(): BelongsTo { return $this->belongsTo(User::class, 'student_id'); }

    public function isGraded(): bool    { return $this->graded_at !== null; }
    public function isSubmitted(): bool { return $this->submitted_at !== null; }
    public function needsRevision(): bool { return (bool) $this->revision_requested; }
}
