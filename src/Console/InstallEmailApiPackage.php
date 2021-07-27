<?php

namespace Rh36\EmailApiPackage\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallEmailApiPackage extends Command
{
    protected $signature = 'emailapi:install';

    protected $description = 'Install the EmailApiPackage';


    public function handle()
    {
        $this->info('Installing EmailApiPackage...');

        $this->info('Publishing configuration...');

        if (!$this->configExists('emailapi.php')) {
            $this->publishConfiguration();
            $this->info('Published configuration');
        } else {
            if ($this->shouldOverwriteConfig()) {
                $this->info('Overwriting configuration file...');
                $this->publishConfiguration($force = true);
            } else {
                $this->info('Existing configuration was not overwritten');
            }
        }


        $this->info('Installed EmailApiPackage');
    }

    private function configExists($fileName)
    {
        return File::exists(config_path($fileName));
    }


    private function shouldOverwriteConfig()
    {
        return $this->confirm(
            'Config file already exists. Do you want to overwrite it?',
            false
        );
    }

    private function publishConfiguration($forcePublish = false)
    {
        $params = [
            '--provider' => "Rh36\EmailApiPackage\Providers\EmailApiPackageServiceProvider",
            '--tag' => "config"
        ];

        if ($forcePublish === true) {
            $params['--force'] = '';
        }

        $this->call('vendor:publish', $params);
    }
}
