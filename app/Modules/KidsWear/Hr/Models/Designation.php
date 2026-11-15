<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Hr\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Designation extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function department(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Hr\Models\Department::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Hr\Models\Employee::class);
    }
}
