<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StyleCosting extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'fabric_cost' => 'decimal:4',
            'trim_cost' => 'decimal:4',
            'cm_cost' => 'decimal:4',
            'washing_cost' => 'decimal:4',
            'print_cost' => 'decimal:4',
            'packing_cost' => 'decimal:4',
            'overhead' => 'decimal:4',
            'margin_percent' => 'decimal:2',
            'fob_price' => 'decimal:4',
        ];
    }

    public function style(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Catalog\Models\Style::class);
    }
}
