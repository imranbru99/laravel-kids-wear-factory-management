<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Production\Services;

use App\Modules\KidsWear\Catalog\Models\BomItem;
use App\Modules\KidsWear\Inventory\Models\Material;
use App\Modules\KidsWear\Production\Models\MaterialRequirement;
use App\Modules\KidsWear\Production\Models\ProductionOrder;

class MaterialRequirementService
{
    public function calculate(ProductionOrder $order): void
    {
        $order->loadMissing('lines.style');
        $order->requirements()->delete();

        /** @var array<string, array{material_id: ?int, name: string, unit: string, qty: float}> $bucket */
        $bucket = [];

        foreach ($order->lines as $line) {
            $pieces = (int) $line->quantity;
            $items = BomItem::query()->where('style_id', $line->style_id)->get();
            foreach ($items as $item) {
                $need = $pieces * (float) $item->consumption * (1 + ((float) $item->wastage_percent / 100));
                $key = ($item->material_id ?: 'n').'|'.$item->unit.'|'.$item->name;
                if (! isset($bucket[$key])) {
                    $bucket[$key] = [
                        'material_id' => $item->material_id,
                        'name' => $item->name,
                        'unit' => $item->unit,
                        'qty' => 0,
                    ];
                }
                $bucket[$key]['qty'] += $need;
            }
        }

        foreach ($bucket as $row) {
            $available = 0.0;
            if ($row['material_id']) {
                $available = (float) Material::query()->find($row['material_id'])?->onHand();
            }
            $shortage = max(0, $row['qty'] - $available);
            MaterialRequirement::query()->create([
                'production_order_id' => $order->id,
                'material_id' => $row['material_id'],
                'name' => $row['name'],
                'unit' => $row['unit'],
                'required_qty' => round($row['qty'], 3),
                'available_qty' => round($available, 3),
                'shortage_qty' => round($shortage, 3),
            ]);
        }
    }
}
