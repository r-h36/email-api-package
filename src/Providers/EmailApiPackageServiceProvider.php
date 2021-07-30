<?php

namespace Rh36\EmailApiPackage\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Rh36\EmailApiPackage\Console\InstallEmailApiPackage;
use Rh36\EmailApiPackage\Providers\EmailApiEventServiceProvider;

use Postmark\PostmarkClient;
use Rh36\EmailApiPackage\Services\PostmarkService;
use Rh36\EmailApiPackage\Jobs\SendByPostmark;

use Mailgun\Mailgun;
use Rh36\EmailApiPackage\Services\MailgunService;
use Rh36\EmailApiPackage\Jobs\SendByMailgun;

use Aws\Ses\SesClient;
use Rh36\EmailApiPackage\Services\SesService;
use Rh36\EmailApiPackage\Jobs\SendBySes;

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

        $this->app->bindMethod([SendByPostmark::class, 'handle'], function ($job, $app) {
            $postmarkClient = new PostmarkClient(config('services.postmark.token'));
            return $job->handle($app->makeWith(PostmarkService::class, ['client' => $postmarkClient]));
        });

        $this->app->bindMethod([SendByMailgun::class, 'handle'], function ($job, $app) {
            $mailgunClient = Mailgun::create(config('services.mailgun.secret'));
            return $job->handle($app->makeWith(MailgunService::class, ['mg' => $mailgunClient]));
        });


        $this->app->bindMethod([SendBySes::class, 'handle'], function ($job, $app) {
            $sesClient = new SesClient([
                'version' => 'latest',
                'region'  => config('services.ses.region'),
                'credentials' => [
                    'key' => config('services.ses.key'),
                    'secret' => config('services.ses.secret'),
                ],
            ]);
            return $job->handle($app->makeWith(SesService::class, ['client' => $sesClient]));
        });
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
