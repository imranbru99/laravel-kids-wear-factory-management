<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Production\Models;

use App\Modules\KidsWear\Support\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionOrder extends Model
{
    use SoftDeletes, RecordsActivity;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'planned_start' => 'date',
            'planned_end' => 'date',
            'actual_start' => 'date',
            'actual_end' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ProductionOrder $order): void {
            if (! $order->number) {
                $order->number = 'PO-'.now()->format('ymd').'-'.str_pad((string) (static::withTrashed()->count() + 1), 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function buyerOrder(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Orders\Models\BuyerOrder::class);
    }

    public function line(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Production\Models\ProductionLine::class, 'line_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Production\Models\ProductionOrderLine::class);
    }

    public function bundles(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Production\Models\Bundle::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Production\Models\ProductionEntry::class);
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Production\Models\MaterialRequirement::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Quality\Models\Inspection::class);
    }

    public function targets(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Production\Models\DailyTarget::class);
    }
}
