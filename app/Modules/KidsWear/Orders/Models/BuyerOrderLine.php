<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuyerOrderLine extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'size_matrix' => 'array',
            'unit_price' => 'decimal:4',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Orders\Models\BuyerOrder::class, 'buyer_order_id');
    }

    public function style(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Catalog\Models\Style::class);
    }

    public function colorway(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Catalog\Models\Colorway::class);
    }
}
