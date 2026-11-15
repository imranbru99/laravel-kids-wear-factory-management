<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Buyer extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function orders(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Orders\Models\BuyerOrder::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Finance\Models\Invoice::class);
    }
}
