<?php

declare(strict_types=1);

return [

    'username' => env('CGRATE_USERNAME'),
    'password' => env('CGRATE_PASSWORD'),
    'endpoint' => 'https://543.cgrate.co.zm/Konik/KonikWs?wsdl',
    'test_endpoint' => 'http://test.543.cgrate.co.zm:55555/Konik/KonikWs?wsdl',
    'test_mode' => env('CGRATE_TEST_MODE', false),

    'options' => [
        'soap_version' => SOAP_1_1,
        'connection_timeout' => 30,
        'keep_alive' => false,
        'cache_wsdl' => WSDL_CACHE_NONE,
        'trace' => env('CGRATE_TEST_MODE', false),
        'exceptions' => env('CGRATE_TEST_MODE', false),
    ],

];
