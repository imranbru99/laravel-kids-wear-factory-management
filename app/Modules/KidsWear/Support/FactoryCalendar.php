<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Support;

use App\Modules\KidsWear\Hr\Models\Holiday;
use Illuminate\Support\Facades\Schema;
use Tarikh\Tarikh;

final class FactoryCalendar
{
    public static function today(): Tarikh
    {
        return Tarikh::today(config('kidswear.timezone', 'Asia/Dhaka'));
    }

    public static function parse(string $date): Tarikh
    {
        return Tarikh::parse($date, config('kidswear.timezone', 'Asia/Dhaka'));
    }

    public static function isWeekend(Tarikh $date): bool
    {
        return in_array((int) $date->format('N'), [5, 6], true);
    }

    public static function isHoliday(Tarikh $date): bool
    {
        if (! Schema::hasTable('holidays')) {
            return false;
        }

        return Holiday::query()->whereDate('observed_on', $date->toDateString())->exists();
    }

    public static function isWorkingDay(Tarikh $date): bool
    {
        return ! self::isWeekend($date) && ! self::isHoliday($date);
    }

    public static function label(Tarikh $date): string
    {
        $formatted = $date->format('d M Y');

        return app()->getLocale() === 'bn' ? bn_digits($formatted) : $formatted;
    }
}
