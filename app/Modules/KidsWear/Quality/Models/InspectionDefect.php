<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Quality\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionDefect extends Model
{
    protected $guarded = ['id'];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Quality\Models\Inspection::class);
    }

    public function defectType(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Quality\Models\DefectType::class);
    }
}
