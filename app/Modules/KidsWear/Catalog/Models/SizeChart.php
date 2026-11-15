<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SizeChart extends Model
{
    protected $guarded = ['id'];

    public function style(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Catalog\Models\Style::class);
    }

    public function rows(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Catalog\Models\SizeChartRow::class);
    }
}
