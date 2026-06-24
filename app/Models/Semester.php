<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Semester extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'academic_year_id',
        'name',
        'type',
        'start_date',
        'end_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Relationships
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function effectiveDay(): HasOne
    {
        return $this->hasOne(EffectiveDay::class);
    }

    /**
     * Scopes
     */
    public function scopeGanjil($query)
    {
        return $query->where('type', 'ganjil');
    }

    public function scopeGenap($query)
    {
        return $query->where('type', 'genap');
    }

    public function scopeActive($query)
    {
        return $query->whereHas('academicYear', function ($q) {
            $q->active();
        });
    }

    /**
     * Helpers
     */
    public function isGanjil(): bool
    {
        return $this->type === 'ganjil';
    }

    public function isGenap(): bool
    {
        return $this->type === 'genap';
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name;
    }
}
