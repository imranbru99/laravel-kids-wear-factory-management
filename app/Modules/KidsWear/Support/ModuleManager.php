<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Support;

use App\Modules\KidsWear\Settings\Models\Setting;
use Illuminate\Support\Facades\Schema;

final class ModuleManager
{
    public static function enabled(string $module): bool
    {
        $defaults = config('kidswear.modules.'.$module.'.enabled', false);
        $stored = self::flags();

        return (bool) ($stored[$module] ?? $defaults);
    }

    /** @return array<string, bool> */
    public static function flags(): array
    {
        if (! Schema::hasTable('settings')) {
            return [];
        }

        $value = Setting::getValue('modules', []);

        return is_array($value) ? $value : [];
    }

    /** @param  array<string, bool>  $flags */
    public static function save(array $flags): void
    {
        $clean = [];
        foreach (array_keys(config('kidswear.modules', [])) as $module) {
            $clean[$module] = (bool) ($flags[$module] ?? false);
        }

        Setting::putValue('modules', $clean, 'system');
    }

    /** @return list<string> */
    public static function permissions(): array
    {
        $permissions = ['dashboard.view', 'settings.view', 'settings.manage'];
        foreach (array_keys(config('kidswear.modules', [])) as $module) {
            if (in_array($module, ['dashboard', 'settings'], true)) {
                continue;
            }
            $permissions[] = $module.'.view';
            $permissions[] = $module.'.manage';
        }

        return array_values(array_unique($permissions));
    }
}
