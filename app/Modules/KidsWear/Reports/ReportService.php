<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Reports;

use App\Modules\KidsWear\Finance\Models\StyleCosting;
use App\Modules\KidsWear\Hr\Models\AttendanceDay;
use App\Modules\KidsWear\Inventory\Models\Material;
use App\Modules\KidsWear\Orders\Models\Buyer;
use App\Modules\KidsWear\Production\Models\ProductionEntry;
use App\Modules\KidsWear\Support\Status;
use Illuminate\Support\Collection;

class ReportService
{
    /** @return array{headers: list<string>, rows: list<list<scalar|null>>} */
    public function build(string $report): array
    {
        return match ($report) {
            'efficiency' => $this->efficiency(),
            'profitability' => $this->profitability(),
            'attendance' => $this->attendance(),
            'inventory' => $this->inventory(),
            'buyers' => $this->buyers(),
            default => abort(404),
        };
    }

    /** @return array{headers: list<string>, rows: list<list<scalar|null>>} */
    private function efficiency(): array
    {
        $rows = ProductionEntry::query()
            ->selectRaw('entry_date, stage, sum(quantity_out) as output, sum(quantity_reject) as rejects')
            ->groupBy('entry_date', 'stage')
            ->orderByDesc('entry_date')
            ->limit(200)
            ->get()
            ->map(fn ($row) => [$row->entry_date, $row->stage, (int) $row->output, (int) $row->rejects])
            ->all();

        return ['headers' => ['Date', 'Stage', 'Output', 'Rejects'], 'rows' => $rows];
    }

    /** @return array{headers: list<string>, rows: list<list<scalar|null>>} */
    private function profitability(): array
    {
        $rows = StyleCosting::query()->with('style')->latest()->limit(200)->get()->map(function (StyleCosting $cost) {
            $made = $cost->fabric_cost + $cost->trim_cost + $cost->cm_cost + $cost->washing_cost + $cost->print_cost + $cost->packing_cost + $cost->overhead;

            return [
                $cost->style?->style_code,
                $cost->style?->name,
                (float) $made,
                (float) $cost->fob_price,
                (float) $cost->margin_percent,
                $cost->currency,
            ];
        })->all();

        return ['headers' => ['Style', 'Name', 'Made cost', 'FOB', 'Margin %', 'Currency'], 'rows' => $rows];
    }

    /** @return array{headers: list<string>, rows: list<list<scalar|null>>} */
    private function attendance(): array
    {
        $rows = AttendanceDay::query()->with('employee')->latest('work_date')->limit(300)->get()->map(fn (AttendanceDay $day) => [
            $day->work_date?->toDateString(),
            $day->employee?->employee_code,
            $day->employee?->name,
            $day->status,
            $day->late_minutes,
            $day->overtime_minutes,
            $day->worked_minutes,
        ])->all();

        return ['headers' => ['Date', 'Code', 'Name', 'Status', 'Late min', 'OT min', 'Worked min'], 'rows' => $rows];
    }

    /** @return array{headers: list<string>, rows: list<list<scalar|null>>} */
    private function inventory(): array
    {
        $rows = Material::query()->orderBy('sku')->get()->map(function (Material $material) {
            $onHand = $material->onHand();

            return [
                $material->sku,
                $material->name,
                $material->type,
                $onHand,
                $material->unit,
                (float) $material->last_cost,
                round($onHand * (float) $material->last_cost, 2),
            ];
        })->all();

        return ['headers' => ['SKU', 'Name', 'Type', 'On hand', 'Unit', 'Last cost', 'Value'], 'rows' => $rows];
    }

    /** @return array{headers: list<string>, rows: list<list<scalar|null>>} */
    private function buyers(): array
    {
        $rows = Buyer::query()->with('orders.lines')->orderBy('name')->get()->map(function (Buyer $buyer) {
            $open = $buyer->orders->whereNotIn('status', [Status::CLOSED, Status::CANCELLED])->count();
            $value = $buyer->orders->flatMap->lines->sum(fn ($line) => $line->quantity * (float) $line->unit_price);

            return [$buyer->code, $buyer->name, $buyer->country, $open, round($value, 2), $buyer->currency];
        })->all();

        return ['headers' => ['Code', 'Buyer', 'Country', 'Open orders', 'Order value', 'Currency'], 'rows' => $rows];
    }

    /** @return Collection<int, string> */
    public function names(): Collection
    {
        return collect([
            'efficiency' => 'Production efficiency',
            'profitability' => 'Style profitability',
            'attendance' => 'Attendance',
            'inventory' => 'Inventory valuation',
            'buyers' => 'Buyer performance',
        ]);
    }
}
