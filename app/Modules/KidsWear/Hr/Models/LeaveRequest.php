<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Hr\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'days' => 'decimal:1',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Hr\Models\Employee::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Hr\Models\LeaveType::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }
}
