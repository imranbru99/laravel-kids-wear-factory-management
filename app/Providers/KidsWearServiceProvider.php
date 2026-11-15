<?php

declare(strict_types=1);

namespace App\Providers;

use App\Modules\KidsWear\Admin\ResourceCatalog;
use App\Modules\KidsWear\Dashboard\DashboardService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use ImranDevBd\AiHub\Facades\AIHub;
use ImranDevBd\AiOcr\Facades\AiOcr;
use ImranDevBD\LaravelAdminPanel\AdminPanel;

class KidsWearServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $map = config('ai-catalog.schema.map', []);
        if (is_array($map)) {
            $map['specs'] = 'specs';
        }
        $json = array_map(
            fn ($column) => $column === 'attributes' ? 'specs' : $column,
            config('ai-catalog.schema.json_columns', []),
        );

        config([
            'ai-catalog.schema.model' => \App\Modules\KidsWear\Catalog\Models\Style::class,
            'ai-catalog.schema.table' => 'styles',
            'ai-catalog.schema.map' => $map,
            'ai-catalog.schema.json_columns' => $json,
            'ai-catalog.taxonomy.model' => \App\Modules\KidsWear\Catalog\Models\Category::class,
        ]);
    }

    public function boot(): void
    {
        View::prependNamespace('admin-panel', resource_path('views/vendor/admin-panel'));

        View::composer('admin-panel::pages.dashboard', function ($view): void {
            $view->with('kpis', app(DashboardService::class)->kpis());
        });

        $this->app->booted(function (): void {
            if (! $this->app->bound(AdminPanel::class)) {
                return;
            }
            $panel = $this->app->make(AdminPanel::class)->getPanel('admin');
            $panel->brandName((string) config('admin-panel.theme.brand_name', 'Rongdhonu Factory'));
            $panel->navigation(ResourceCatalog::navigation());
        });

        Gate::before(function ($user) {
            return $user->hasRole('super-admin') ? true : null;
        });

        AIHub::auth(function ($request): bool {
            $user = $request->user();

            return $user && $user->hasAnyRole(['super-admin', 'factory-manager', 'sales', 'quality-controller']);
        });

        AiOcr::auth(function ($request): bool {
            $user = $request->user();

            return $user && $user->hasAnyRole(['super-admin', 'factory-manager', 'sales']);
        });
    }
}
