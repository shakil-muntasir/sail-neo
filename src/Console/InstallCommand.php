<?php

namespace Ronvolt\SailNeo\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sail:install {--php= : Set PHP version to 7.4/8.0 | Default/Fallback - 8.0} {--with= : The services that should be included in the installation} \
    {--mysql= : Set MySQL version to 5.7/8.0 if added as service | Ignored if service is not added, default/fallback - 8.0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Laravel Sail\'s default Docker Compose file';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->option('php')) {
            if ($this->option('php') == "8.0") {
                $php_version = "8.0";
            } elseif ($this->option('php') == "7.4") {
                $php_version = "7.4";
            } else {
                $this->error('Unsupported version provided! Defaulting to PHP version 8.0');
                $php_version = "8.0";
            }
        } else {
            $php_version = "8.0";
        }

        if ($this->option('with')) {
            $services = $this->option('with') == 'none' ? [] : explode(',', $this->option('with'));
        } elseif ($this->option('no-interaction')) {
            $services = ['mysql', 'redis', 'selenium', 'mailhog'];
        } else {
            $services = $this->gatherServicesWithSymfonyMenu();
        }

        $mysql_version = "8.0";
        if (in_array("mysql", $services)) {
            if ($this->option('mysql')) {
                if ($this->option('mysql') == "8.0") {
                    $mysql_version = "8.0";
                } elseif ($this->option('mysql') == "5.7") {
                    $mysql_version = "5.7";
                } else {
                    $this->error('Unsupported version provided! Defaulting to MySQL version 8.0');
                    $mysql_version = "8.0";
                }
            } else {
                $mysql_version = "8.0";
            }
        }

        $this->buildDockerCompose($php_version, $mysql_version, $services);
        $this->replaceEnvVariables($services);

        $this->info('Sail scaffolding installed successfully.');
    }

    /**
     * Gather the desired Sail services using a Symfony menu.
     *
     * @return array
     */
    protected function gatherServicesWithSymfonyMenu()
    {
        return $this->choice('Which services would you like to install?', [
             'mysql',
             'pgsql',
             'mariadb',
             'redis',
             'memcached',
             'meilisearch',
             'mailhog',
             'selenium',
         ], 0, null, true);
    }

    /**
     * Build the Docker Compose file.
     *
     * @param  array  $services
     * @return void
     */
    protected function buildDockerCompose($php_version, $mysql_version, array $services)
    {
        $depends = collect($services)
            ->filter(function ($service) {
                return in_array($service, ['mysql', 'pgsql', 'mariadb', 'redis', 'selenium']);
            })->map(function ($service) {
                return "            - {$service}";
            })->whenNotEmpty(function ($collection) {
                return $collection->prepend('depends_on:');
            })->implode("\n");

        $stubs = rtrim(collect($services)->map(function ($service) {
            return file_get_contents(__DIR__ . "/../../stubs/{$service}.stub");
        })->implode(''));

        $stubs = str_replace('{{mysql_version}}', $mysql_version, $stubs);

        $volumes = collect($services)
            ->filter(function ($service) {
                return in_array($service, ['mysql', 'pgsql', 'mariadb', 'redis', 'meilisearch']);
            })->map(function ($service) {
                return "    sail{$service}:\n        driver: local";
            })->whenNotEmpty(function ($collection) {
                return $collection->prepend('volumes:');
            })->implode("\n");

        $dockerCompose = file_get_contents(__DIR__ . '/../../stubs/docker-compose.stub');

        $dockerCompose = str_replace('{{php_version}}', $php_version, $dockerCompose);
        $dockerCompose = str_replace('{{depends}}', empty($depends) ? '' : '        '.$depends, $dockerCompose);
        $dockerCompose = str_replace('{{services}}', $stubs, $dockerCompose);
        $dockerCompose = str_replace('{{volumes}}', $volumes, $dockerCompose);

        // Remove empty lines...
        $dockerCompose = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $dockerCompose);

        file_put_contents($this->laravel->basePath('docker-compose.yml'), $dockerCompose);
    }

    /**
     * Replace the Host environment variables in the app's .env file.
     *
     * @param  array  $services
     * @return void
     */
    protected function replaceEnvVariables(array $services)
    {
        $environment = file_get_contents($this->laravel->basePath('.env'));

        if (in_array('pgsql', $services)) {
            $environment = str_replace('DB_CONNECTION=mysql', "DB_CONNECTION=pgsql", $environment);
            $environment = str_replace('DB_HOST=127.0.0.1', "DB_HOST=pgsql", $environment);
            $environment = str_replace('DB_PORT=3306', "DB_PORT=5432", $environment);
        } elseif (in_array('mariadb', $services)) {
            $environment = str_replace('DB_HOST=127.0.0.1', "DB_HOST=mariadb", $environment);
        } else {
            $environment = str_replace('DB_HOST=127.0.0.1', "DB_HOST=mysql", $environment);
        }

        $environment = str_replace('DB_USERNAME=root', "DB_USERNAME=sail", $environment);
        $environment = preg_replace("/DB_PASSWORD=(.*)/", "DB_PASSWORD=password", $environment);

        $environment = str_replace('MEMCACHED_HOST=127.0.0.1', 'MEMCACHED_HOST=memcached', $environment);
        $environment = str_replace('REDIS_HOST=127.0.0.1', 'REDIS_HOST=redis', $environment);

        if (in_array('meilisearch', $services)) {
            $environment .= "\nSCOUT_DRIVER=meilisearch";
            $environment .= "\nMEILISEARCH_HOST=http://meilisearch:7700\n";
        }

        file_put_contents($this->laravel->basePath('.env'), $environment);
    }
}
