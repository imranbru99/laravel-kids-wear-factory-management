<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Hr\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'paid' => 'boolean',
        ];
    }

    public function requests(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Hr\Models\LeaveRequest::class);
    }
}
