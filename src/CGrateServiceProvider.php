<?php

declare(strict_types=1);

namespace CGrate\Laravel;

use CGrate\Laravel\Console\Commands\CheckAccountBalance;
use CGrate\Php\Config\CGrateConfig;
use CGrate\Php\Services\CGrateService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

final class CGrateServiceProvider extends ServiceProvider
{
    protected array $commands = [
        CheckAccountBalance::class,
    ];

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/cgrate.php', 'cgrate');

        $this->app->singleton('CGrate', function (): CGrateService {
            $config = Config::get('cgrate');

            return new CGrateService(
                CGrateConfig::create(
                    $config['username'],
                    $config['password'],
                    $config['test_mode'],
                    $config['options'],
                )
            );
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/cgrate.php' => config_path(path: 'cgrate.php'),
            ], 'config');

            $this->commands($this->commands);
        }
    }
}
