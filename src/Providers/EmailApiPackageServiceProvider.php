<?php

namespace Rh36\EmailApiPackage\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Rh36\EmailApiPackage\Console\InstallEmailApiPackage;
use Rh36\EmailApiPackage\Providers\EmailApiEventServiceProvider;

class EmailApiPackageServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(EmailApiEventServiceProvider::class);
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


            if (!class_exists('CreateEmailTemplatesTable')) {
                $this->publishes([
                    __DIR__ . '/../../database/migrations/create_email_templates_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_email_templates_table.php'),
                    __DIR__ . '/../../database/migrations/create_email_logs_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_email_logs_table.php'),
                    __DIR__ . '/../../database/migrations/create_email_api_jobs_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_email_api_jobs_table.php'),
                ], 'migrations');
            }
        }

        $this->loadRoutesFrom(__DIR__ . '/../../routes/emailapi.php');
        $this->registerRoutes();
    }

    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../../routes/emailapi.php');
        });
    }

    protected function routeConfiguration()
    {
        return [
            'prefix' => 'emailapi',
        ];
    }
}
