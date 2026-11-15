<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Modules\KidsWear\Support\AlertService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ScanFactoryAlertsJob implements ShouldQueue
{
    use Queueable;

    public function handle(AlertService $alerts): void
    {
        $alerts->scan();
    }
}
