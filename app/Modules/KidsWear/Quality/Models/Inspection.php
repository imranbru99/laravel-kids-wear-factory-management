<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Quality\Models;

use App\Modules\KidsWear\Support\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Inspection extends Model implements HasMedia
{
    use SoftDeletes, RecordsActivity, InteractsWithMedia;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'inspected_at' => 'datetime',
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

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'inspector_id');
    }

    public function defects(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Quality\Models\InspectionDefect::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos');
    }
}
