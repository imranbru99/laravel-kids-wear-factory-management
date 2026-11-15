<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Inventory\Models;

use App\Modules\KidsWear\Support\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use SoftDeletes, RecordsActivity;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'reorder_level' => 'decimal:3',
            'last_cost' => 'decimal:4',
            'width_cm' => 'decimal:2',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Procurement\Models\Supplier::class, 'preferred_supplier_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Inventory\Models\Stock::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Inventory\Models\StockMovement::class);
    }

    public function bomItems(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Catalog\Models\BomItem::class);
    }

    public function onHand(): float
    {
        return (float) $this->stocks()->sum('on_hand');
    }
}
