<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Production\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialRequirement extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'required_qty' => 'decimal:3',
            'available_qty' => 'decimal:3',
            'shortage_qty' => 'decimal:3',
            'issued_qty' => 'decimal:3',
        ];
    }

    public function productionOrder(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Production\Models\ProductionOrder::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Inventory\Models\Material::class);
    }
}
