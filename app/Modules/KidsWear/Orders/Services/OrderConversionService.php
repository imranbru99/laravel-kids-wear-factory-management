<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Orders\Services;

use App\Modules\KidsWear\Orders\Models\BuyerOrder;
use App\Modules\KidsWear\Production\Models\ProductionOrder;
use App\Modules\KidsWear\Production\Services\BundleService;
use App\Modules\KidsWear\Production\Services\MaterialRequirementService;
use App\Modules\KidsWear\Support\Status;
use Illuminate\Support\Facades\DB;

class OrderConversionService
{
    public function __construct(
        private readonly MaterialRequirementService $materials,
        private readonly BundleService $bundles,
    ) {}

    public function convert(BuyerOrder $order): ProductionOrder
    {
        return DB::transaction(function () use ($order) {
            $order->loadMissing('lines');
            $existing = ProductionOrder::query()->where('buyer_order_id', $order->id)->first();
            if ($existing) {
                return $existing;
            }

            $production = ProductionOrder::query()->create([
                'buyer_order_id' => $order->id,
                'status' => Status::PLANNED,
                'planned_start' => now()->toDateString(),
                'planned_end' => $order->delivery_date,
                'priority' => $order->delivery_date->lte(now()->addDays(21)) ? 'rush' : 'normal',
                'remarks' => 'Converted from buyer order '.$order->number,
            ]);

            foreach ($order->lines as $line) {
                $production->lines()->create([
                    'style_id' => $line->style_id,
                    'colorway_id' => $line->colorway_id,
                    'size_matrix' => $line->size_matrix,
                    'quantity' => $line->quantity,
                ]);
            }

            $this->materials->calculate($production);
            $this->bundles->generate($production);

            if ($order->status === Status::DRAFT || $order->status === Status::CONFIRMED) {
                $order->forceFill([
                    'status' => Status::IN_PRODUCTION,
                    'confirmed_at' => $order->confirmed_at ?? now(),
                ])->save();
            }

            return $production->load('lines', 'requirements', 'bundles');
        });
    }
}
