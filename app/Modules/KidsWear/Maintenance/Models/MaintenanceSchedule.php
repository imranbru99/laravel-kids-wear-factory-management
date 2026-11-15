<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Maintenance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceSchedule extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'last_done_on' => 'date',
            'next_due_on' => 'date',
        ];
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Maintenance\Models\Machine::class);
    }
}
