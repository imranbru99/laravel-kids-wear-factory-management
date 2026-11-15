<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Procurement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OcrIntake extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'parsed' => 'array',
        ];
    }

    public function buyerOrder(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Orders\Models\BuyerOrder::class);
    }
}
