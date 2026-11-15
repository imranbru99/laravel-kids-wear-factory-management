<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'on_hand' => 'decimal:3',
            'reserved' => 'decimal:3',
        ];
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Inventory\Models\Material::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Inventory\Models\Location::class);
    }
}
