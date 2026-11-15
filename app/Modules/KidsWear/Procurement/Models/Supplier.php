<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Procurement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Procurement\Models\PurchaseOrder::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Inventory\Models\Material::class, 'preferred_supplier_id');
    }
}
