<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Support;

use App\Modules\KidsWear\Settings\Models\Setting;

final class Company
{
    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        return [
            'name' => 'Rongdhonu Kids Wear Ltd.',
            'name_bn' => 'রংধনু কিডস ওয়্যার লিমিটেড',
            'tagline' => 'Export kids wear, cut and sewn in Gazipur.',
            'tagline_bn' => 'গাজীপুরে তৈরি রপ্তানিমানের শিশু পোশাক।',
            'address' => 'Plot 18, Konabari BSCIC, Gazipur 1700, Bangladesh',
            'address_bn' => 'প্লট ১৮, কোনাবাড়ী বিসিক, গাজীপুর ১৭০০, বাংলাদেশ',
            'phone' => '+880 2 5500 1840',
            'email' => 'sales@rongdhonu.test',
            'website' => 'https://rongdhonu.test',
            'established' => 2008,
            'workers' => 860,
            'lines' => 12,
            'capacity' => '420,000 pcs / month',
        ];
    }

    /** @return array<string, mixed> */
    public static function profile(): array
    {
        $stored = Setting::getValue('company', []);

        return array_merge(self::defaults(), is_array($stored) ? $stored : []);
    }

    /** @param  array<string, mixed>  $profile */
    public static function save(array $profile): void
    {
        Setting::putValue('company', array_merge(self::profile(), $profile), 'company');
    }
}
