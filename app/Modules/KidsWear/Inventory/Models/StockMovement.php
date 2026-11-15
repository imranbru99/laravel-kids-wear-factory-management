<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Inventory\Models;

use App\Modules\KidsWear\Support\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    use RecordsActivity;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
