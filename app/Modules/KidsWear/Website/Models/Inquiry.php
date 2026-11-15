<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Website\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inquiry extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function style(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Catalog\Models\Style::class);
    }
}
