<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Production\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionEntry extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
        ];
    }

    public function productionOrder(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Production\Models\ProductionOrder::class);
    }

    public function bundle(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Production\Models\Bundle::class);
    }

    public function line(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Production\Models\ProductionLine::class, 'line_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Hr\Models\Employee::class, 'operator_employee_id');
    }
}
