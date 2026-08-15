<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PklPlacementMove extends Model
{
    protected $fillable = [
        'pkl_placement_id', 'from_company_id', 'to_company_id', 'reason', 'moved_by',
    ];

    public function placement(): BelongsTo { return $this->belongsTo(PklPlacement::class, 'pkl_placement_id'); }
    public function fromCompany(): BelongsTo { return $this->belongsTo(PklCompany::class, 'from_company_id'); }
    public function toCompany(): BelongsTo { return $this->belongsTo(PklCompany::class, 'to_company_id'); }
    public function movedBy(): BelongsTo { return $this->belongsTo(User::class, 'moved_by'); }
}