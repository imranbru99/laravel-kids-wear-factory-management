<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Hr\Services;

use App\Modules\KidsWear\Hr\Models\AttendanceDay;
use App\Modules\KidsWear\Hr\Models\Employee;
use App\Modules\KidsWear\Hr\Models\Shift;
use App\Modules\KidsWear\Support\FactoryCalendar;
use Carbon\Carbon;
use ImranDevBd\AttendanceHub\DTOs\AttendanceShift;
use ImranDevBd\AttendanceHub\Facades\AttendanceHub;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    public function checkIn(Employee $employee, string $method, ?float $lat = null, ?float $lng = null, string $punchType = 'check_in'): void
    {
        $payload = [
            'employee_id' => $employee->employee_code,
            'method' => $method,
            'punch_type' => $punchType,
        ];

        if ($method === 'gps') {
            $payload['lat'] = $lat;
            $payload['lng'] = $lng;
            $payload['target_lat'] = (float) config('kidswear.factory.lat');
            $payload['target_lng'] = (float) config('kidswear.factory.lng');
            $payload['radius_meters'] = (int) config('kidswear.factory.radius_meters', 250);
        }

        try {
            AttendanceHub::provider('virtual')->recordCheckIn($payload);
        } catch (ValidationException $exception) {
            throw $exception;
        }

        $this->rollup($employee, FactoryCalendar::today()->toDateString());
    }

    public function rollup(Employee $employee, string $date): AttendanceDay
    {
        $shift = $employee->shift ?: Shift::query()->first();
        $dto = new AttendanceShift(
            name: $shift->name ?? 'General',
            startTime: $shift->start_time ?? '08:00',
            endTime: $shift->end_time ?? '17:00',
            gracePeriodMinutes: (int) ($shift->grace_minutes ?? 10),
            halfDayThresholdMinutes: (int) ($shift->half_day_minutes ?? 240),
            fullDayThresholdMinutes: (int) ($shift->full_day_minutes ?? 480),
            breakDurationMinutes: (int) ($shift->break_minutes ?? 60),
            allowOvertime: (bool) ($shift->overtime_allowed ?? true),
        );

        $summary = AttendanceHub::calculator($dto)->calculate($employee->employee_code, Carbon::parse($date));

        return AttendanceDay::query()->updateOrCreate(
            ['employee_id' => $employee->id, 'work_date' => $date],
            [
                'status' => $summary->status,
                'late_minutes' => (int) $summary->lateMinutes,
                'early_leave_minutes' => (int) ($summary->earlyLeaveMinutes ?? 0),
                'worked_minutes' => (int) $summary->totalWorkedMinutes,
                'overtime_minutes' => (int) $summary->overtimeMinutes,
                'shift_name' => $summary->shiftName,
                'source' => 'attendance-hub',
            ],
        );
    }

    public function rollupDate(string $date): int
    {
        $count = 0;
        Employee::query()->where('status', 'active')->orderBy('id')->each(function (Employee $employee) use ($date, &$count): void {
            $this->rollup($employee, $date);
            $count++;
        });

        return $count;
    }
}
