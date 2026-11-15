<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Dashboard;

use App\Modules\KidsWear\Hr\Models\AttendanceDay;
use App\Modules\KidsWear\Inventory\Models\Material;
use App\Modules\KidsWear\Orders\Models\BuyerOrder;
use App\Modules\KidsWear\Production\Models\ProductionEntry;
use App\Modules\KidsWear\Production\Models\ProductionOrder;
use App\Modules\KidsWear\Quality\Models\Inspection;
use App\Modules\KidsWear\Support\Status;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\Models\Activity;

class DashboardService
{
    /** @return array<string, mixed> */
    public function kpis(): array
    {
        $today = now()->toDateString();
        $output = (int) ProductionEntry::query()->whereDate('entry_date', $today)->sum('quantity_out');
        $target = (int) \App\Modules\KidsWear\Production\Models\DailyTarget::query()->whereDate('target_date', $today)->sum('target_qty');
        $efficiency = $target > 0 ? round(($output / $target) * 100, 1) : ($output > 0 ? 100 : 0);

        $attendance = AttendanceDay::query()->whereDate('work_date', $today);
        $present = (clone $attendance)->whereIn('status', [Status::PRESENT, Status::LATE, Status::HALF_DAY])->count();
        $late = (clone $attendance)->where('status', Status::LATE)->count();
        $absent = (clone $attendance)->where('status', Status::ABSENT)->count();

        $sample = (int) Inspection::query()->where('inspected_at', '>=', now()->subDays(7))->sum('sample_size');
        $rejected = (int) Inspection::query()->where('inspected_at', '>=', now()->subDays(7))->sum('rejected_qty');
        $rejection = $sample > 0 ? round(($rejected / $sample) * 100, 1) : 0;

        $completed = ProductionOrder::query()->where('status', Status::COMPLETED);
        $onTimeBase = (clone $completed)->count();
        $onTime = (clone $completed)->whereColumn('actual_end', '<=', 'planned_end')->count();

        $materialAlerts = Material::query()->where('reorder_level', '>', 0)->get()->filter(
            fn (Material $material) => $material->onHand() <= (float) $material->reorder_level
        )->count();

        return [
            'output' => $output,
            'efficiency' => $efficiency,
            'pending_orders' => BuyerOrder::query()->whereNotIn('status', [Status::CLOSED, Status::CANCELLED, Status::SHIPPED])->count(),
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'material_alerts' => $materialAlerts,
            'rejection' => $rejection,
            'on_time' => $onTimeBase > 0 ? round(($onTime / $onTimeBase) * 100, 1) : 0,
            'delayed' => ProductionOrder::query()->where('status', Status::DELAYED)->count(),
            'activity' => Schema::hasTable('activity_log')
                ? Activity::query()->latest()->limit(6)->get()
                : collect(),
        ];
    }
}
