<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Modules\KidsWear\Settings\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\File;

class BackupDatabaseJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $dir = storage_path('app/backups');
        File::ensureDirectoryExists($dir);
        $stamp = now()->format('Ymd-His');
        $connection = config('database.default');

        if ($connection === 'sqlite') {
            $source = config('database.connections.sqlite.database');
            $target = $dir.'/sqlite-'.$stamp.'.sqlite';
            if (is_string($source) && is_file($source)) {
                File::copy($source, $target);
                Setting::putValue('last_backup', ['path' => $target, 'at' => now()->toDateTimeString()], 'system');
            }

            return;
        }

        Setting::putValue('last_backup', [
            'at' => now()->toDateTimeString(),
            'note' => 'MySQL backups should be taken with mysqldump against '.config('database.connections.mysql.database').'.',
        ], 'system');
    }
}
