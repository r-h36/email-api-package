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

        $this->info('Publishing migrations...');

        if (!$this->migrationExists('migrations/2021_07_27_140000_create_email_logs_table.php')) {
            $this->publishMigration();
            $this->info('Published migrations');
        } else {
            if ($this->shouldOverwriteMigration()) {
                $this->info('Overwriting migration files...');
                $this->publishMigration(true);
            } else {
                $this->info('Existing migration files were not overwritten');
            }
        }


        $this->info('Installed EmailApiPackage');
    }

    private function migrationExists($fileName)
    {
        return File::exists(database_path($fileName));
    }

    private function shouldOverwriteMigration()
    {
        return $this->confirm(
            'Migration files already exist. Do you want to overwrite them?',
            false
        );
    }

    private function publishMigration($forcePublish = false)
    {
        $params = [
            '--provider' => "Rh36\EmailApiPackage\Providers\EmailApiPackageServiceProvider",
            '--tag' => "migrations"
        ];

        if ($forcePublish === true) {
            $params['--force'] = '';
        }

        $this->call('vendor:publish', $params);
    }
}
