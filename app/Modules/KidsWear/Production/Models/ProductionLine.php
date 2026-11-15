<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Production\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionLine extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Hr\Models\Employee::class, 'supervisor_employee_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Production\Models\ProductionOrder::class, 'line_id');
    }

    public function machines(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Maintenance\Models\Machine::class, 'line_id');
    }
}
