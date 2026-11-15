<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Inventory\Services;

use App\Modules\KidsWear\Inventory\Models\Location;
use App\Modules\KidsWear\Inventory\Models\Material;
use App\Modules\KidsWear\Inventory\Models\Stock;
use App\Modules\KidsWear\Inventory\Models\StockMovement;
use App\Modules\KidsWear\Production\Models\ProductionOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockService
{
    public function receive(Material $material, Location $location, float $quantity, string $lot = 'OPEN', ?string $note = null, mixed $reference = null): Stock
    {
        return $this->move($material, $location, 'in', $quantity, $lot, $note, $reference);
    }

    public function issueToOrder(ProductionOrder $order, ?Location $location = null): void
    {
        $location ??= Location::query()->where('type', 'store')->first();
        if (! $location) {
            throw ValidationException::withMessages(['location' => 'Create a store location before issuing material.']);
        }

        DB::transaction(function () use ($order, $location): void {
            $order->loadMissing('requirements.material');
            foreach ($order->requirements as $requirement) {
                $pending = (float) $requirement->required_qty - (float) $requirement->issued_qty;
                if ($pending <= 0 || ! $requirement->material) {
                    continue;
                }
                $available = (float) $requirement->material->onHand();
                if ($available + 0.0001 < $pending) {
                    throw ValidationException::withMessages([
                        'stock' => $requirement->name.' is short by '.round($pending - $available, 3).' '.$requirement->unit.'.',
                    ]);
                }
                $this->move($requirement->material, $location, 'consume', $pending, 'OPEN', 'Issued to '.$order->number, $order);
                $requirement->update([
                    'issued_qty' => (float) $requirement->required_qty,
                    'available_qty' => $requirement->material->onHand(),
                    'shortage_qty' => 0,
                ]);
            }
        });
    }

    public function move(Material $material, ?Location $location, string $type, float $quantity, string $lot = 'OPEN', ?string $note = null, mixed $reference = null): Stock
    {
        return DB::transaction(function () use ($material, $location, $type, $quantity, $lot, $note, $reference) {
            $stock = Stock::query()->firstOrCreate(
                [
                    'material_id' => $material->id,
                    'location_id' => $location?->id,
                    'lot_no' => $lot,
                ],
                ['on_hand' => 0, 'reserved' => 0],
            );

            $delta = in_array($type, ['out', 'consume'], true) ? -$quantity : $quantity;
            $next = (float) $stock->on_hand + $delta;
            if ($next < -0.0001) {
                throw ValidationException::withMessages([
                    'stock' => $material->name.' does not have enough stock in '.($location->name ?? 'the selected location').'.',
                ]);
            }

            $stock->update(['on_hand' => round($next, 3)]);

            $movement = new StockMovement([
                'material_id' => $material->id,
                'location_id' => $location?->id,
                'type' => $type,
                'quantity' => $quantity,
                'note' => $note,
                'user_id' => auth()->id(),
            ]);
            if ($reference) {
                $movement->reference()->associate($reference);
            }
            $movement->save();

            return $stock->refresh();
        });
    }
}
