<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PklMaterial extends Model
{
    protected $fillable = [
        'pkl_course_id', 'title', 'type', 'file_path',
        'external_url', 'file_size', 'order',
    ];

    public function course(): BelongsTo { return $this->belongsTo(PklCourse::class, 'pkl_course_id'); }

    public function getIconAttribute(): string
    {
        return match($this->type) {
            'pdf' => 'fas fa-file-pdf text-red-500',
            'video' => 'fas fa-play-circle text-blue-500',
            'link' => 'fas fa-external-link-alt text-green-500',
            'document' => 'fas fa-file-word text-blue-600',
            'image' => 'fas fa-image text-purple-500',
            default => 'fas fa-file text-gray-500',
        };
    }

    public function getFileSizeHumanAttribute(): string
    {
        if (!$this->file_size) return '-';
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $i = 0;
        while ($size >= 1024 && $i < 3) { $size /= 1024; $i++; }
        return round($size, 1) . ' ' . $units[$i];
    }
}
