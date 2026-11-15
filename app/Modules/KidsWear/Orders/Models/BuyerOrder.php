<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Orders\Models;

use App\Modules\KidsWear\Support\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BuyerOrder extends Model
{
    use SoftDeletes, RecordsActivity;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'delivery_date' => 'date',
            'confirmed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (BuyerOrder $order): void {
            if (! $order->number) {
                $order->number = 'BO-'.now()->format('ymd').'-'.str_pad((string) (static::withTrashed()->count() + 1), 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Orders\Models\Buyer::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Orders\Models\BuyerOrderLine::class);
    }

    public function productionOrders(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Production\Models\ProductionOrder::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Finance\Models\Invoice::class);
    }

    public function value(): float
    {
        return (float) $this->lines->sum(fn ($line) => $line->quantity * (float) $line->unit_price);
    }
}
