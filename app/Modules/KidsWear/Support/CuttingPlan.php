<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Support;

final class CuttingPlan
{
    /**
     * Reduce a kids size run to the smallest integer marker ratio.
     *
     * @param  array<string, int>  $matrix
     * @return array{ratio: array<string, int>, lays: int, gcd: int}
     */
    public static function suggest(array $matrix): array
    {
        $matrix = SizeMatrix::normalize($matrix);
        $quantities = array_values($matrix);
        $gcd = self::gcdList($quantities);
        $ratio = [];
        foreach ($matrix as $size => $qty) {
            $ratio[$size] = $gcd > 0 ? intdiv($qty, $gcd) : 0;
        }

        return [
            'ratio' => $ratio,
            'lays' => $gcd,
            'gcd' => $gcd,
        ];
    }

    /** @param  list<int>  $numbers */
    private static function gcdList(array $numbers): int
    {
        $numbers = array_values(array_filter($numbers, fn (int $n) => $n > 0));
        if ($numbers === []) {
            return 0;
        }

        $gcd = $numbers[0];
        foreach ($numbers as $number) {
            $gcd = self::gcd($gcd, $number);
        }

        return $gcd;
    }

    private static function gcd(int $a, int $b): int
    {
        while ($b !== 0) {
            [$a, $b] = [$b, $a % $b];
        }

        return $a;
    }
}
