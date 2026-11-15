<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Procurement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoodsReceipt extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'received_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (GoodsReceipt $receipt): void {
            if (! $receipt->number) {
                $receipt->number = 'GRN-'.now()->format('ymd').'-'.str_pad((string) (static::query()->count() + 1), 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Procurement\Models\PurchaseOrder::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Inventory\Models\Location::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'received_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Procurement\Models\GoodsReceiptItem::class);
    }
}
