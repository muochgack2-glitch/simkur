<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeachingMaterial extends Model
{
    use HasFactory;

    protected static function booted()
    {
        // Auto-update TeacherSubjectRequirement setelah save
        static::saved(function ($material) {
            if ($material->created_by && $material->subject_id && $material->academic_year_id) {
                TeacherSubjectRequirement::updateFromMaterials(
                    $material->created_by,
                    $material->subject_id,
                    $material->academic_year_id
                );
            }
        });

        // Auto-update TeacherSubjectRequirement setelah delete
        static::deleted(function ($material) {
            if ($material->created_by && $material->subject_id && $material->academic_year_id) {
                TeacherSubjectRequirement::updateFromMaterials(
                    $material->created_by,
                    $material->subject_id,
                    $material->academic_year_id
                );
            }
        });
    }

    protected $fillable = [
        'title',
        'description',
        'category',
        'subject_id',
        'academic_year_id',
        'grade',
        'phase',
        'semester',
        'file_type',
        'file_path',
        'file_size',
        'external_link',
        'dimension_1_beriman',
        'dimension_2_kebinekaan',
        'dimension_3_gotong_royong',
        'dimension_4_mandiri',
        'dimension_5_bernalar_kritis',
        'dimension_6_kreatif',
        'dimension_7_numerasi',
        'dimension_8_literasi',
        'tags',
        'target_class_ids',
        'is_public',
        'download_count',
        'view_count',
        'status',
        'approval_notes',
        'approved_by',
        'approved_at',
        'created_by',
        'updated_by',
        'parent_material_id',
        'version_number',
        'revision_notes',
    ];

    protected $casts = [
        'tags' => 'array',
        'target_class_ids' => 'array',
        'is_public' => 'boolean',
        'dimension_1_beriman' => 'boolean',
        'dimension_2_kebinekaan' => 'boolean',
        'dimension_3_gotong_royong' => 'boolean',
        'dimension_4_mandiri' => 'boolean',
        'dimension_5_bernalar_kritis' => 'boolean',
        'dimension_6_kreatif' => 'boolean',
        'dimension_7_numerasi' => 'boolean',
        'dimension_8_literasi' => 'boolean',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function shares(): HasMany
    {
        return $this->hasMany(TeachingMaterialShare::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TeachingMaterialComment::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TeachingMaterialAttachment::class);
    }

    // Version relationships
    public function parentMaterial(): BelongsTo
    {
        return $this->belongsTo(TeachingMaterial::class, 'parent_material_id');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(TeachingMaterial::class, 'parent_material_id');
    }

    public function latestRevision()
    {
        return $this->revisions()->latest('version_number')->first();
    }

    public function allVersions()
    {
        if ($this->parent_material_id) {
            // If this is a revision, get all siblings + parent
            return TeachingMaterial::where('parent_material_id', $this->parent_material_id)
                ->orWhere('id', $this->parent_material_id)
                ->orderBy('version_number', 'desc')
                ->get();
        } else {
            // If this is parent, get all revisions + self
            return TeachingMaterial::where('parent_material_id', $this->id)
                ->orWhere('id', $this->id)
                ->orderBy('version_number', 'desc')
                ->get();
        }
    }

    // Constants
    public const CATEGORIES = [
        // Perencanaan (7)
        'cp' => 'CP (Capaian Pembelajaran)',
        'atp' => 'ATP (Alur Tujuan Pembelajaran)',
        'kktp' => 'KKTP (Kriteria Ketercapaian Tujuan Pembelajaran)',
        'prota' => 'PROTA (Program Tahunan)',
        'prosem' => 'PROSEM (Program Semester)',
        'modul_ajar' => 'Modul Ajar',
        'modul_projek' => 'Modul Projek',
        
        // Media & Bahan Ajar (4)
        'buku_teks' => 'Buku Teks / E-Book',
        'video_pembelajaran' => 'Video Pembelajaran',
        'presentasi_infografis' => 'Presentasi / Infografis',
        'bahan_bacaan' => 'Bahan Bacaan / Artikel',
        
        // Asesmen (4)
        'bank_soal' => 'Bank Soal / Paket Soal',
        'rubrik_penilaian_umum' => 'Rubrik Penilaian Umum',
        'asesmen_diagnostik' => 'Asesmen Diagnostik',
        'instrumen_uji_kompetensi' => 'Instrumen Uji Kompetensi',
        
        // Remedial & Pengayaan (2)
        'program_remedial' => 'Program Remedial',
        'program_pengayaan' => 'Program Pengayaan',
        
        // Kokurikuler SMK (3)
        'job_sheet' => 'Job Sheet / Panduan Praktikum',
        'teaching_factory' => 'Teaching Factory',
        'pkl' => 'PKL (Praktik Kerja Lapangan)',
    ];

    public const CATEGORY_GROUPS = [
        'Perencanaan' => ['cp', 'atp', 'kktp', 'prota', 'prosem', 'modul_ajar', 'modul_projek'],
        'Media & Bahan Ajar' => ['buku_teks', 'video_pembelajaran', 'presentasi_infografis', 'bahan_bacaan'],
        'Asesmen' => ['bank_soal', 'rubrik_penilaian_umum', 'asesmen_diagnostik', 'instrumen_uji_kompetensi'],
        'Remedial & Pengayaan' => ['program_remedial', 'program_pengayaan'],
        'Kokurikuler SMK' => ['job_sheet', 'teaching_factory', 'pkl'],
    ];

    // Helper Methods
    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }
    
    public function getCategoryGroupAttribute(): string
    {
        foreach (self::CATEGORY_GROUPS as $group => $categories) {
            if (in_array($this->category, $categories)) {
                return $group;
            }
        }
        return 'Lainnya';
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'draft' => 'Draft',
            'pending_approval' => 'Menunggu Approval',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        $colors = [
            'draft' => 'gray',
            'pending_approval' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function getSelectedDimensionsAttribute(): array
    {
        $dimensions = [];
        
        if ($this->dimension_1_beriman) $dimensions[] = 'Beriman & Bertakwa';
        if ($this->dimension_2_kebinekaan) $dimensions[] = 'Kebinekaan Global';
        if ($this->dimension_3_gotong_royong) $dimensions[] = 'Gotong Royong';
        if ($this->dimension_4_mandiri) $dimensions[] = 'Mandiri';
        if ($this->dimension_5_bernalar_kritis) $dimensions[] = 'Bernalar Kritis';
        if ($this->dimension_6_kreatif) $dimensions[] = 'Kreatif';
        if ($this->dimension_7_numerasi) $dimensions[] = 'Numerasi';
        if ($this->dimension_8_literasi) $dimensions[] = 'Literasi';

        return $dimensions;
    }

    public function getFileSizeFormattedAttribute(): string
    {
        if (!$this->file_size) return '-';

        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }

    // Attachment Helper Methods
    public function getTotalAttachmentsAttribute(): int
    {
        return $this->attachments()->count();
    }

    public function getTotalFileSizeAttribute(): int
    {
        return $this->attachments()->sum('file_size');
    }

    public function getTotalFileSizeFormattedAttribute(): string
    {
        $bytes = $this->getTotalFileSizeAttribute();
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function getPrimaryAttachmentAttribute()
    {
        return $this->attachments()->where('is_primary', true)->first();
    }

    public function hasAttachments(): bool
    {
        return $this->attachments()->exists();
    }

    // Version helper methods
    public function isRevision(): bool
    {
        return !is_null($this->parent_material_id);
    }

    public function hasRevisions(): bool
    {
        return $this->revisions()->exists();
    }

    public function getVersionLabelAttribute(): string
    {
        if ($this->isRevision()) {
            return "v{$this->version_number} (Revisi)";
        }
        
        if ($this->hasRevisions()) {
            $latestVersion = $this->latestRevision();
            return "v{$this->version_number} (Ada {$this->revisions()->count()} revisi, terbaru: v{$latestVersion->version_number})";
        }
        
        return "v{$this->version_number}";
    }

    public function canBeEdited(): bool
    {
        // Draft dan Rejected bisa di-edit langsung
        return in_array($this->status, ['draft', 'rejected']);
    }

    public function canCreateRevision(): bool
    {
        // Approved materials bisa create revision
        return $this->status === 'approved';
    }

    public function canBeWithdrawn(): bool
    {
        // Pending materials bisa di-withdraw
        return $this->status === 'pending_approval';
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
}
