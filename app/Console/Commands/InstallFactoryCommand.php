<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InstallFactoryCommand extends Command
{
    protected $signature = 'kidswear:install {--fresh : Drop all tables and reseed}';

    protected $description = 'Migrate and seed the kids wear factory demo';

    public function handle(): int
    {
        if ($this->option('fresh')) {
            $this->call('migrate:fresh', ['--seed' => true, '--force' => true]);
        } else {
            $this->call('migrate', ['--force' => true]);
            $this->call('db:seed', ['--force' => true]);
        }

        $this->info('Rongdhonu Kids Wear is ready. Sign in at /login with super@rongdhonu.test / password');

        return self::SUCCESS;
    }
}
