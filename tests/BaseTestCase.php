<?php

namespace Rh36\EmailApiPackage\Tests;

use Rh36\EmailApiPackage\Providers\EmailApiPackageServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class BaseTestCase extends OrchestraTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
    }

    protected function getPackageProviders($app)
    {
        return [
            EmailApiPackageServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // import the CreateEmailTemplateTable class from the migration
        include_once __DIR__ . '/../database/migrations/create_users_table.php.stub';
        include_once __DIR__ . '/../database/migrations/create_email_templates_table.php.stub';
        include_once __DIR__ . '/../database/migrations/create_email_logs_table.php.stub';

        // run the up() method of that migration class
        (new \CreateUsersTable)->up();
        (new \CreateEmailTemplatesTable)->up();
        (new \CreateEmailLogsTable)->up();
    }
}
