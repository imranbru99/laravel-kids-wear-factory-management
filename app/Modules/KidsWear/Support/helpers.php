<?php

use App\Modules\KidsWear\Settings\Models\Setting;
use App\Modules\KidsWear\Support\Company;
use App\Modules\KidsWear\Support\ModuleManager;

if (! function_exists('kidswear')) {
    function kidswear(?string $key = null, mixed $default = null): mixed
    {
        $company = Company::profile();

        return $key === null ? $company : data_get($company, $key, $default);
    }
}

if (! function_exists('module_enabled')) {
    function module_enabled(string $module): bool
    {
        return ModuleManager::enabled($module);
    }
}

if (! function_exists('bn_digits')) {
    function bn_digits(int|float|string|null $value): string
    {
        $map = ['0' => '০', '1' => '১', '2' => '২', '3' => '৩', '4' => '৪', '5' => '৫', '6' => '৬', '7' => '৭', '8' => '৮', '9' => '৯'];

        return strtr((string) $value, $map);
    }
}

if (! function_exists('locale_number')) {
    function locale_number(int|float|string|null $value): string
    {
        $formatted = is_numeric($value) ? number_format((float) $value, str_contains((string) $value, '.') ? 2 : 0) : (string) $value;

        return app()->getLocale() === 'bn' ? bn_digits($formatted) : $formatted;
    }
}

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::getValue($key, $default);
    }
}
