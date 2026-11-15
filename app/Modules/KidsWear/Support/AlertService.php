<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Support;

use App\Models\User;
use App\Modules\KidsWear\Inventory\Models\Material;
use App\Modules\KidsWear\Maintenance\Models\MaintenanceSchedule;
use App\Modules\KidsWear\Production\Models\ProductionOrder;
use App\Modules\KidsWear\Quality\Models\Inspection;
use App\Notifications\FactoryAlertNotification;
use Illuminate\Support\Facades\Notification;

class AlertService
{
    /** @return list<string> */
    public function scan(): array
    {
        $messages = [];

        ProductionOrder::query()
            ->whereDate('planned_end', '<', now()->toDateString())
            ->whereNotIn('status', [Status::COMPLETED, Status::CANCELLED])
            ->each(function (ProductionOrder $order) use (&$messages): void {
                $order->update(['status' => Status::DELAYED]);
                $messages[] = $order->number.' is past '.$order->planned_end->toDateString().'.';
            });

        Material::query()->where('reorder_level', '>', 0)->each(function (Material $material) use (&$messages): void {
            if ($material->onHand() <= (float) $material->reorder_level) {
                $messages[] = $material->sku.' '.$material->name.' is at or below reorder level.';
            }
        });

        $sample = (int) Inspection::query()->where('inspected_at', '>=', now()->subDays(7))->sum('sample_size');
        $rejected = (int) Inspection::query()->where('inspected_at', '>=', now()->subDays(7))->sum('rejected_qty');
        if ($sample > 0) {
            $rate = ($rejected / $sample) * 100;
            $limit = (float) config('kidswear.factory.rejection_alert_percent', 5);
            if ($rate >= $limit) {
                $messages[] = 'Seven-day rejection rate is '.number_format($rate, 1).'%, above the '.$limit.'% limit.';
            }
        }

        MaintenanceSchedule::query()
            ->whereDate('next_due_on', '<=', now()->toDateString())
            ->with('machine')
            ->each(function (MaintenanceSchedule $schedule) use (&$messages): void {
                $messages[] = ($schedule->machine->code ?? 'Machine').' maintenance due: '.$schedule->task.'.';
            });

        if ($messages !== []) {
            $users = User::role(['super-admin', 'factory-manager'])->get();
            Notification::send($users, new FactoryAlertNotification(
                'Factory alerts',
                implode(' ', array_slice($messages, 0, 6)),
                'warning',
                url('/admin'),
            ));
        }

        return $messages;
    }
}
