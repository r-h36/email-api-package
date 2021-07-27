<?php

namespace Rh36\EmailApiPackage\Providers;

use Illuminate\Support\ServiceProvider;
use Rh36\EmailApiPackage\Console\InstallEmailApiPackage;

class EmailApiPackageServiceProvider extends ServiceProvider
{
    public function register()
    {
        # code...
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallEmailApiPackage::class,
            ]);

            $this->publishes([
                __DIR__ . '/../../config/emailapi.php' => config_path('emailapi.php'),
            ], 'config');

            $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        }
    }
}
