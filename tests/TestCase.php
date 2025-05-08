<?php

declare(strict_types=1);

namespace Cgrate\Laravel\Tests;

use Cgrate\Laravel\CgrateServiceProvider;
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
            CgrateServiceProvider::class,
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

        // Set Cgrate config
        $app['config']->set('cgrate.username', 'test_username');
        $app['config']->set('cgrate.password', 'test_password');
        $app['config']->set('cgrate.test_mode', true);
        $app['config']->set('cgrate.options', [
            'soap_version' => SOAP_1_1,
            'connection_timeout' => 30,
            'keep_alive' => false,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'trace' => true,
            'exceptions' => true,
        ]);
    }
}
