<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Support;

final class SizeMatrix
{
    /** @return list<string> */
    public static function sizes(): array
    {
        return config('kidswear.sizes', []);
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, int>
     */
    public static function normalize(array $input): array
    {
        $matrix = [];
        foreach (self::sizes() as $size) {
            $qty = (int) ($input[$size] ?? 0);
            if ($qty > 0) {
                $matrix[$size] = $qty;
            }
        }

        return $matrix;
    }

    /** @param  array<string, int|string>  $matrix */
    public static function total(array $matrix): int
    {
        return (int) array_sum(array_map('intval', $matrix));
    }

    /** @param  array<string, int>  $matrix */
    public static function label(array $matrix): string
    {
        $parts = [];
        foreach ($matrix as $size => $qty) {
            if ((int) $qty > 0) {
                $parts[] = $size.':'.$qty;
            }
        }

        return implode(' · ', $parts);
    }
}
