<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'year',
        'start_date',
        'end_date',
        'is_active',
        'is_archived',
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
            'is_active' => 'boolean',
            'is_archived' => 'boolean',
        ];
    }

    /**
     * Relationships
     */
    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_archived', false);
    }

    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    public function scopeNotArchived($query)
    {
        return $query->where('is_archived', false);
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // When activating a year, deactivate all others
        static::saving(function ($academicYear) {
            if ($academicYear->is_active && $academicYear->isDirty('is_active')) {
                static::where('id', '!=', $academicYear->id)->update(['is_active' => false]);
            }
        });

        // Auto-create semesters when academic year is created
        static::created(function ($academicYear) {
            $academicYear->createDefaultSemesters();
        });
    }

    /**
     * Create default semesters (Ganjil & Genap)
     */
    public function createDefaultSemesters(): void
    {
        // Semester Ganjil (Juli - Desember)
        $this->semesters()->create([
            'name' => "Semester Ganjil {$this->year}",
            'type' => 'ganjil',
            'start_date' => $this->start_date,
            'end_date' => $this->start_date->copy()->addMonths(5)->endOfMonth(),
        ]);

        // Semester Genap (Januari - Juni)
        $this->semesters()->create([
            'name' => "Semester Genap {$this->year}",
            'type' => 'genap',
            'start_date' => $this->start_date->copy()->addMonths(6),
            'end_date' => $this->end_date,
        ]);
    }

    /**
     * Get the display name
     */
    public function getDisplayNameAttribute(): string
    {
        return "Tahun Pelajaran {$this->year}";
    }
}
