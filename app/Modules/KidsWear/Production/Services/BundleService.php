<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Production\Services;

use App\Modules\KidsWear\Production\Models\Bundle;
use App\Modules\KidsWear\Production\Models\ProductionOrder;
use App\Modules\KidsWear\Support\Status;

class BundleService
{
    public function generate(ProductionOrder $order): int
    {
        if ($order->bundles()->exists()) {
            return $order->bundles()->count();
        }

        $order->loadMissing('lines');
        $size = max(1, (int) config('kidswear.factory.bundle_size', 20));
        $created = 0;

        foreach ($order->lines as $line) {
            foreach ($line->size_matrix ?? [] as $sizeCode => $qty) {
                $remaining = (int) $qty;
                while ($remaining > 0) {
                    $take = min($size, $remaining);
                    Bundle::query()->create([
                        'production_order_id' => $order->id,
                        'style_id' => $line->style_id,
                        'colorway_id' => $line->colorway_id,
                        'size_code' => $sizeCode,
                        'quantity' => $take,
                        'current_stage' => Status::CUTTING,
                        'status' => Status::OPEN,
                    ]);
                    $remaining -= $take;
                    $created++;
                }
            }
        }

        return $created;
    }
}
