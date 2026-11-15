<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Finance\Services;

use App\Modules\KidsWear\Catalog\Models\Style;
use App\Modules\KidsWear\Finance\Models\StyleCosting;

class CostingService
{
    /** @param  array<string, float|int|string|null>  $input */
    public function save(Style $style, array $input): StyleCosting
    {
        $fabric = (float) ($input['fabric_cost'] ?? 0);
        $trim = (float) ($input['trim_cost'] ?? 0);
        $cm = (float) ($input['cm_cost'] ?? 0);
        $washing = (float) ($input['washing_cost'] ?? 0);
        $print = (float) ($input['print_cost'] ?? 0);
        $packing = (float) ($input['packing_cost'] ?? 0);
        $overhead = (float) ($input['overhead'] ?? 0);
        $margin = (float) ($input['margin_percent'] ?? 10);
        $base = $fabric + $trim + $cm + $washing + $print + $packing + $overhead;
        $fob = round($base * (1 + ($margin / 100)), 4);

        return $style->costings()->create([
            'fabric_cost' => $fabric,
            'trim_cost' => $trim,
            'cm_cost' => $cm,
            'washing_cost' => $washing,
            'print_cost' => $print,
            'packing_cost' => $packing,
            'overhead' => $overhead,
            'margin_percent' => $margin,
            'fob_price' => $fob,
            'currency' => $input['currency'] ?? 'USD',
            'notes' => $input['notes'] ?? null,
        ]);
    }
}
