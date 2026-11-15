<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Production\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyTarget extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'target_date' => 'date',
        ];
    }

    public function productionOrder(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Production\Models\ProductionOrder::class);
    }

    public function line(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Production\Models\ProductionLine::class, 'line_id');
    }
}
