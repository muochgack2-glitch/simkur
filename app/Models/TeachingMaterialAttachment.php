<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeachingMaterialAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'teaching_material_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'external_link',
        'attachment_type',
        'is_primary',
        'description',
        'sort_order',
        'download_count',
        'uploaded_by',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'file_size' => 'integer',
        'download_count' => 'integer',
        'sort_order' => 'integer',
    ];

    // Constants
    public const ATTACHMENT_TYPES = [
        'main' => 'Dokumen Utama',
        'lkpd' => 'LKPD (Lembar Kerja)',
        'presentation' => 'Presentasi/Slide',
        'video' => 'Video Pembelajaran',
        'assessment' => 'Instrumen Asesmen',
        'rubric' => 'Rubrik Penilaian',
        'answer_key' => 'Kunci Jawaban',
        'reading_material' => 'Bahan Bacaan',
        'other' => 'Lainnya',
    ];

    // Relationships
    public function teachingMaterial(): BelongsTo
    {
        return $this->belongsTo(TeachingMaterial::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Helper Methods
    public function getAttachmentTypeLabelAttribute(): string
    {
        return self::ATTACHMENT_TYPES[$this->attachment_type] ?? $this->attachment_type;
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

    public function getFileIconAttribute(): string
    {
        return match($this->file_type) {
            'pdf' => '📄',
            'docx' => '📝',
            'pptx' => '📊',
            'xlsx' => '📈',
            'jpg', 'png' => '🖼️',
            'mp4' => '🎬',
            'link' => '🔗',
            default => '📎',
        };
    }

    public function isLink(): bool
    {
        return $this->file_type === 'link' && !empty($this->external_link);
    }

    public function isFile(): bool
    {
        return $this->file_type !== 'link' && !empty($this->file_path);
    }

    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }

    // Scopes
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('attachment_type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('is_primary', 'desc')->orderBy('sort_order')->orderBy('created_at');
    }
}
