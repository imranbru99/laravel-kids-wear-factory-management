<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Maintenance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Machine extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'installed_on' => 'date',
        ];
    }

    public function line(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Production\Models\ProductionLine::class, 'line_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Maintenance\Models\MaintenanceSchedule::class);
    }

    public function breakdowns(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Maintenance\Models\BreakdownLog::class);
    }
}
