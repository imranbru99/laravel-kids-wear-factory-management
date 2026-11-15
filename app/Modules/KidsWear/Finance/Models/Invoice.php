<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Finance\Models;

use App\Modules\KidsWear\Support\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes, RecordsActivity;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'subtotal' => 'decimal:2',
            'freight' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice): void {
            if (! $invoice->number) {
                $prefix = $invoice->type === 'packing_list' ? 'PL' : 'INV';
                $invoice->number = $prefix.'-'.now()->format('ymd').'-'.str_pad((string) (static::withTrashed()->count() + 1), 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Orders\Models\Buyer::class);
    }

    public function buyerOrder(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Orders\Models\BuyerOrder::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Finance\Models\InvoiceItem::class);
    }
}
