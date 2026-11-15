<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Hr\Models;

use App\Modules\KidsWear\Support\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes, RecordsActivity;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'joined_on' => 'date',
            'basic_salary' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Employee $employee): void {
            if (! $employee->employee_code) {
                $next = static::withTrashed()->count() + 1;
                $employee->employee_code = 'EMP-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Hr\Models\Department::class);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Hr\Models\Designation::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\KidsWear\Hr\Models\Shift::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Hr\Models\LeaveRequest::class);
    }

    public function attendanceDays(): HasMany
    {
        return $this->hasMany(\App\Modules\KidsWear\Hr\Models\AttendanceDay::class);
    }
}
