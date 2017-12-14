<?php
/**
 * Bootstrap & init everything
 */

Dotenv::load(__DIR__.'/');

return [
    'paths'        => [
        'migrations' => __DIR__.'/migrations',
    ],
    'environments' => [
        'default_database' => 'development',
        'development' => [
            'adapter' => 'mysql',
            'host'    => getenv('DB_HOST'),
            'user'    => getenv('DB_USER'),
            'pass'    => getenv('DB_PASS'),
            'port'    => 3306,
            'name'    => getenv('DB_DATABASE'),
            'charset' => 'utf8',
        ],
        'testing'  => [
            'adapter' => 'mysql',
            'host'    => getenv('DB_HOST'),
            'user'    => getenv('DB_USER'),
            'pass'    => getenv('DB_PASS'),
            'port'    => 3306,
            'name'    => getenv('DB_TEST_DATABASE'),
            'charset' => 'utf8',
        ],
    ],
];
