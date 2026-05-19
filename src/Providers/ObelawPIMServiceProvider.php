<?php

namespace Obelaw\Ium\Eam\Providers;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;

class ObelawEAMServiceProvider extends ServiceProvider
{
    public function register()
    {
        // 
    }

    public function boot()

    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

            AboutCommand::add('Obelawium', fn() => ['Obelawium EAM' => '0.1.0']);
        }
    }
}
