<?php

namespace Rh36\EmailApiPackage\Tests\Unit;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Rh36\EmailApiPackage\Tests\Unit\BaseTestCase;

class InstallEmailApiPackageTest extends BaseTestCase
{
    /** @test */
    function the_install_command_copies_the_configuration()
    {
        // make sure we're starting from a clean state
        if (File::exists(config_path('emailapi.php'))) {
            unlink(config_path('emailapi.php'));
        }

        $this->assertFalse(File::exists(config_path('emailapi.php')));

        $this->artisan('emailapi:install')
            ->expectsOutput('Installing EmailApiPackage...')
            ->assertExitCode(0);

        $this->assertTrue(File::exists(config_path('emailapi.php')));
    }
}
