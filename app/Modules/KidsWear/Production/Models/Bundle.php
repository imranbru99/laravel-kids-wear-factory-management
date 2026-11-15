<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Production\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bundle extends Model
{
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::creating(function (Bundle $bundle): void {
            if (! $bundle->code) {
                $bundle->code = 'BND-'.now()->format('ymd').'-'.strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
            }
        });
    }

    public function productionOrder(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Production\Models\ProductionOrder::class);
    }

    public function style(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Catalog\Models\Style::class);
    }

    public function colorway(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Catalog\Models\Colorway::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Production\Models\ProductionEntry::class);
    }
}
