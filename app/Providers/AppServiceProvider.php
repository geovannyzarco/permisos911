<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Policies\AprobacionPermisoPolicy;
use Illuminate\Support\Facades\Gate;
use Filament\Tables\Table;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('aprobarVB', [AprobacionPermisoPolicy::class, 'aprobarVB']);
        Gate::define('aprobarFinal', [AprobacionPermisoPolicy::class, 'aprobarFinal']);
        Gate::define('aprobarJefeDivision', [AprobacionPermisoPolicy::class, 'aprobarJefeDivision']);

        Table::configureUsing(function (Table $table): void {
            $table->defaultKeySort(function () use ($table) {
                $model = $table->getModel();
                if (! $model) {
                    return true;
                }
                $keyName = app($model)->getKeyName();
                return $table->getSortColumn() !== $keyName;
            });
        });
    }
}
