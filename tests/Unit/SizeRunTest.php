<?php

namespace Tests\Unit;

use App\Modules\KidsWear\Support\CuttingPlan;
use App\Modules\KidsWear\Support\SizeMatrix;
use Tests\TestCase;

class SizeRunTest extends TestCase
{
    public function test_a_kids_size_run_reduces_to_a_marker_ratio(): void
    {
        $matrix = SizeMatrix::normalize([
            '2Y' => 400,
            '4Y' => 600,
            '6Y' => 600,
            '8Y' => 400,
            '10Y' => 0,
        ]);

        $this->assertSame(2000, SizeMatrix::total($matrix));

        $plan = CuttingPlan::suggest($matrix);

        $this->assertSame(['2Y' => 2, '4Y' => 3, '6Y' => 3, '8Y' => 2], $plan['ratio']);
        $this->assertSame(200, $plan['lays']);
    }
}
