<?php

use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php'; 

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

return [
   'paths' => [
        'migrations' => './migrations', 
        'seeds' => './seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'production',
        'production' => [
            'adapter' => 'mysql',
            'host' => $_ENV['DB_HOST'],
            'name' => $_ENV['DB_NAME'],
            'user' => $_ENV['DB_USER'],
            'pass' => $_ENV['DB_PASSWORD'],
            'port' => $_ENV['DB_PORT'],
            'charset' => 'utf8mb4',
        ]
    ]
];
