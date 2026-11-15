<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Hr\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'observed_on' => 'date',
            'paid' => 'boolean',
        ];
    }
}
