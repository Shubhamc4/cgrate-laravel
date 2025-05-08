<?php

declare(strict_types=1);

use Cgrate\Laravel\Services\CgrateService;

it('registers the Cgrate service', function (): void {
    $service = app('cgrate');
    expect($service)->toBeInstanceOf(CgrateService::class);
});

it('can access the Cgrate service through the facade', function (): void {
    expect(fn () => Cgrate\Laravel\Facades\Cgrate::getFacadeRoot())
        ->not->toThrow(Exception::class);
});

it('merges the Cgrate configuration', function (): void {
    $config = config('cgrate');
    expect($config)->toBeArray()
        ->and($config)->toHaveKeys(['username', 'password', 'endpoint', 'test_endpoint', 'test_mode', 'options'])
        ->and($config['test_mode'])->toBeTrue();
});
