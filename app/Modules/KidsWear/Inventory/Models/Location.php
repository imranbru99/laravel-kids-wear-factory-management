<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $guarded = ['id'];

    public function stocks(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Inventory\Models\Stock::class);
    }
}
