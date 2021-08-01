<?php

namespace Rh36\EmailApiPackage\Tests\Unit;

use Illuminate\Support\Facades\File;
use Rh36\EmailApiPackage\Providers\EmailApiPackageServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class InstallEmailApiPackageTest extends OrchestraTestCase
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

    /** @test */
    function the_install_command_copies_the_configuration()
    {
        $mig1 = database_path('migrations/2021_07_27_140000_create_email_logs_table.php');
        $mig2 = database_path('migrations/2021_07_27_142000_create_email_templates_table.php');

        // make sure we're starting from a clean state
        if (File::exists($mig1)) {
            unlink($mig1);
        }
        if (File::exists($mig2)) {
            unlink($mig2);
        }

        $this->assertFalse(File::exists($mig1));
        $this->assertFalse(File::exists($mig2));

        $this->artisan('emailapi:install')
            ->expectsOutput('Installing EmailApiPackage...')
            ->assertExitCode(0);

        $this->assertTrue(File::exists($mig1));
        $this->assertTrue(File::exists($mig2));

        unlink($mig1);
        unlink($mig2);
    }
}
