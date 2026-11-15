<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Modules\KidsWear\Hr\Services\AttendanceService;
use App\Modules\KidsWear\Support\FactoryCalendar;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RollupAttendanceJob implements ShouldQueue
{
    use Queueable;

    public function handle(AttendanceService $attendance): void
    {
        $attendance->rollupDate(FactoryCalendar::today()->toDateString());
    }
}
