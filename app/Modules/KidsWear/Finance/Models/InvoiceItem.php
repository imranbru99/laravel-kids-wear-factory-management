<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'size_matrix' => 'array',
            'unit_price' => 'decimal:4',
            'amount' => 'decimal:2',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Finance\Models\Invoice::class);
    }

    public function style(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Catalog\Models\Style::class);
    }
}
