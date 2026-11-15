<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgeGroup extends Model
{
    protected $guarded = ['id'];

    public function styles(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Catalog\Models\Style::class);
    }
}
