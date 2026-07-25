<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeachingMaterialComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'teaching_material_id',
        'user_id',
        'comment',
    ];

    public function teachingMaterial(): BelongsTo
    {
        return $this->belongsTo(TeachingMaterial::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
