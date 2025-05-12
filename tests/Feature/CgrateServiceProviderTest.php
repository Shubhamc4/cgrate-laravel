<?php

declare(strict_types=1);

use CGrate\Laravel\Facades\CGrate;
use CGrate\Php\Services\CGrateService;

it('registers the CGrate service', function (): void {
    $service = app('CGrate');
    expect($service)->toBeInstanceOf(CGrateService::class);
});

it('can access the CGrate service through the facade', function (): void {
    expect(fn () => CGrate::getFacadeRoot())
        ->not->toThrow(Exception::class);
});

it('merges the CGrate configuration', function (): void {
    $config = config('cgrate');
    expect($config)->toBeArray()
        ->and($config)->toHaveKeys(['username', 'password', 'test_mode', 'options'])
        ->and($config['test_mode'])->toBeTrue();
});
