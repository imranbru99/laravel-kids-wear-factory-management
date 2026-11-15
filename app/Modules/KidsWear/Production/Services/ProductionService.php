<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Production\Services;

use App\Modules\KidsWear\Inventory\Models\FinishedGood;
use App\Modules\KidsWear\Inventory\Models\Location;
use App\Modules\KidsWear\Production\Models\Bundle;
use App\Modules\KidsWear\Production\Models\ProductionEntry;
use App\Modules\KidsWear\Production\Models\ProductionOrder;
use App\Modules\KidsWear\Support\Status;

class ProductionService
{
    /** @param  array<string, mixed>  $data */
    public function record(ProductionOrder $order, array $data): ProductionEntry
    {
        $entry = $order->entries()->create([
            'bundle_id' => $data['bundle_id'] ?? null,
            'line_id' => $data['line_id'] ?? $order->line_id,
            'stage' => $data['stage'],
            'entry_date' => $data['entry_date'] ?? now()->toDateString(),
            'quantity_in' => (int) ($data['quantity_in'] ?? 0),
            'quantity_out' => (int) ($data['quantity_out'] ?? 0),
            'quantity_reject' => (int) ($data['quantity_reject'] ?? 0),
            'operator_employee_id' => $data['operator_employee_id'] ?? null,
            'remarks' => $data['remarks'] ?? null,
        ]);

        if ($entry->bundle_id) {
            Bundle::query()->whereKey($entry->bundle_id)->update([
                'current_stage' => $entry->stage,
                'status' => $entry->stage === Status::PACKING ? Status::COMPLETED : Status::IN_PROCESS,
            ]);
        }

        $entry->load('bundle');
        if ($entry->stage === Status::PACKING && $entry->quantity_out > 0 && $entry->bundle) {
            $location = Location::query()->where('type', 'fg')->first() ?: Location::query()->first();
            if ($location) {
                $good = FinishedGood::query()->firstOrCreate(
                    [
                        'style_id' => $entry->bundle->style_id,
                        'colorway_id' => $entry->bundle->colorway_id,
                        'location_id' => $location->id,
                        'size_code' => $entry->bundle->size_code,
                    ],
                    ['quantity' => 0],
                );
                $good->increment('quantity', $entry->quantity_out);
            }
        }

        $this->refreshStatus($order);

        return $entry;
    }

    public function refreshStatus(ProductionOrder $order): void
    {
        $order->loadMissing('lines', 'entries');
        $ordered = (int) $order->lines->sum('quantity');
        $packed = (int) $order->entries->where('stage', Status::PACKING)->sum('quantity_out');
        $status = $order->status;

        if ($packed > 0 && $packed >= $ordered) {
            $status = Status::COMPLETED;
            $order->actual_end = $order->actual_end ?: now()->toDateString();
        } elseif ($order->entries->isNotEmpty()) {
            $status = (string) $order->entries->sortByDesc('id')->first()->stage;
            $order->actual_start = $order->actual_start ?: now()->toDateString();
        }

        if ($order->planned_end && $order->planned_end->lt(now()->startOfDay()) && $status !== Status::COMPLETED) {
            $status = Status::DELAYED;
        }

        $order->status = $status;
        $order->save();
    }
}
