<?php

declare(strict_types=1);

namespace Cgrate\Laravel;

use Cgrate\Laravel\Console\Commands\CheckAccountBalance;
use Cgrate\Laravel\Services\CgrateService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

final class CgrateServiceProvider extends ServiceProvider
{
    /**
     * The commands to be registered.
     *
     * @var array<class-string>
     */
    protected array $commands = [
        CheckAccountBalance::class,
    ];

    /**
     * Register the Cgrate service with the Laravel container.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/cgrate.php', 'cgrate');

        $this->app->singleton('cgrate', function ($app) {
            return new CgrateService(Config::get('cgrate'));
        });
    }

    /**
     * Bootstrap the Cgrate service.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/cgrate.php' => config_path('cgrate.php'),
            ], 'config');

            $this->commands($this->commands);
        }
    }
}
