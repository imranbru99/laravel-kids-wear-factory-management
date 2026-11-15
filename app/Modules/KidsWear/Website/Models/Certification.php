<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Website\Models;

use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'valid_until' => 'date',
            'is_public' => 'boolean',
        ];
    }
}
