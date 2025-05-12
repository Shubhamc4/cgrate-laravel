<?php

declare(strict_types=1);

namespace CGrate\Laravel\Tests;

use CGrate\Laravel\CGrateServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            CGrateServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function defineEnvironment($app): void
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Set CGrate config
        $app['config']->set('cgrate', config('cgrate'));
        $app['config']->set('cgrate.username', 'test_username');
        $app['config']->set('cgrate.password', 'test_password');
        $app['config']->set('cgrate.test_mode', true);
    }
}
