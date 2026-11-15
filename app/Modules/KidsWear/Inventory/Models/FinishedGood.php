<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinishedGood extends Model
{
    protected $guarded = ['id'];

    public function style(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Catalog\Models\Style::class);
    }

    public function colorway(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Catalog\Models\Colorway::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Inventory\Models\Location::class);
    }
}
