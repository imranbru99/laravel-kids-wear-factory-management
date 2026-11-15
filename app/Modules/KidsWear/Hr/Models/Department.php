<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Hr\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function designations(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Hr\Models\Designation::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Hr\Models\Employee::class);
    }
}
