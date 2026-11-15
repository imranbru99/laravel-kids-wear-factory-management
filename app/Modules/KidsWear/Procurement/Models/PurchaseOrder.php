<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Procurement\Models;

use App\Modules\KidsWear\Support\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes, RecordsActivity;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'expected_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (PurchaseOrder $order): void {
            if (! $order->number) {
                $order->number = 'PUR-'.now()->format('ymd').'-'.str_pad((string) (static::withTrashed()->count() + 1), 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Procurement\Models\Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Procurement\Models\PurchaseOrderItem::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Procurement\Models\GoodsReceipt::class);
    }
}
