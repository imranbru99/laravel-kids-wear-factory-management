<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SizeChartRow extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'measurements' => 'array',
        ];
    }

    public function sizeChart(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Catalog\Models\SizeChart::class);
    }
}
