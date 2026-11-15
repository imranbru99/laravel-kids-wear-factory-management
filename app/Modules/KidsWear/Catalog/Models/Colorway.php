<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Colorway extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function style(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Catalog\Models\Style::class);
    }
}
