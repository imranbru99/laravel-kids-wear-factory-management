<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Procurement\Services;

use App\Modules\KidsWear\Inventory\Models\Location;
use App\Modules\KidsWear\Inventory\Services\StockService;
use App\Modules\KidsWear\Procurement\Models\GoodsReceipt;
use App\Modules\KidsWear\Procurement\Models\PurchaseOrder;
use App\Modules\KidsWear\Support\Status;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReceiptService
{
    public function __construct(private readonly StockService $stock) {}

    public function receiveAll(PurchaseOrder $order, Location $location): GoodsReceipt
    {
        return DB::transaction(function () use ($order, $location) {
            $order->loadMissing('items.material');
            $receipt = GoodsReceipt::query()->create([
                'purchase_order_id' => $order->id,
                'received_at' => now(),
                'location_id' => $location->id,
                'received_by' => auth()->id(),
            ]);

            $receivedSomething = false;
            foreach ($order->items as $item) {
                $pending = (float) $item->quantity - (float) $item->received_qty;
                if ($pending <= 0 || ! $item->material) {
                    continue;
                }
                $receipt->items()->create([
                    'purchase_order_item_id' => $item->id,
                    'material_id' => $item->material_id,
                    'quantity' => $pending,
                    'lot_no' => $order->number,
                ]);
                $this->stock->receive($item->material, $location, $pending, $order->number, 'GRN '.$receipt->number, $receipt);
                $item->update([
                    'received_qty' => (float) $item->quantity,
                ]);
                $item->material->update(['last_cost' => $item->unit_price]);
                $receivedSomething = true;
            }

            if (! $receivedSomething) {
                throw ValidationException::withMessages(['receipt' => 'Nothing is left to receive on this purchase order.']);
            }

            $order->update(['status' => Status::RECEIVED]);

            return $receipt->load('items');
        });
    }
}
