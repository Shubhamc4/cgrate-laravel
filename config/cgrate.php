<?php

declare(strict_types=1);

return [

    'username' => env('CGRATE_USERNAME'),
    'password' => env('CGRATE_PASSWORD'),
    'test_mode' => env('CGRATE_TEST_MODE', false),

    'options' => [
        'soap_version' => SOAP_1_1,
        'connection_timeout' => 30,
        'keep_alive' => false,
        'cache_wsdl' => WSDL_CACHE_NONE,
        'exceptions' => true,
        'trace' => env('CGRATE_TEST_MODE', false),
    ],

];
