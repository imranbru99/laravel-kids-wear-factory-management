<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Hr\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    protected $guarded = ['id'];

    public function employees(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Hr\Models\Employee::class);
    }
}
