<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeachingMaterialShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'teaching_material_id',
        'shared_with_user_id',
        'shared_with_class_id',
        'can_edit',
        'can_download',
    ];

    protected $casts = [
        'can_edit' => 'boolean',
        'can_download' => 'boolean',
    ];

    public function teachingMaterial(): BelongsTo
    {
        return $this->belongsTo(TeachingMaterial::class);
    }

    public function sharedWithUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_with_user_id');
    }

    public function sharedWithClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'shared_with_class_id');
    }
}
