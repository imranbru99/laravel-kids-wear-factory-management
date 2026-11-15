<?php

use App\Jobs\RollupAttendanceJob;
use App\Jobs\ScanFactoryAlertsJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new ScanFactoryAlertsJob)->dailyAt('18:10');
Schedule::job(new RollupAttendanceJob)->dailyAt('20:15');
