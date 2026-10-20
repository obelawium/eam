<?php

declare(strict_types=1);

namespace Obelaw\Ium\Eam\Providers;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use Obelaw\Ium\Eam\EamManager;
use Obelaw\Ium\Facades\Ium;

class EamServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/eam.php', 'eam');

        Ium::registerDomain('eam', EamManager::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

            $this->publishes([
                __DIR__ . '/../../database/migrations' => database_path('migrations'),
            ], 'ium-eam-migrations');

            $this->publishes([
                __DIR__ . '/../../config/eam.php' => config_path('eam.php'),
            ], 'ium-eam-config');

            AboutCommand::add('Obelawium', fn() => ['Obelawium EAM' => '0.1.0']);
        }
    }
}
