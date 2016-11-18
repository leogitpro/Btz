<?php

use Zend\Log\Logger;

return [
    'log' => [
        'appLogger' => [
            'writers' => [
                [
                    'name' => 'stream',
                    'priority' => Logger::DEBUG,
                    'options' => [
                        'stream' => rtrim(sys_get_temp_dir(), "/\\") . DIRECTORY_SEPARATOR . 'log-' . date('Ymd') . '.txt',
                        //'formatter' => [
                        //    'name' => 'appFormatter',
                        //],
                        //'formatter' => '%timestamp% %priorityName% (%priority%): %message% %extra%',
                    ],
                ], //First writer end
            ],
        ],
    ],

    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => [
                    'host' => '127.0.0.1',
                    'port' => '3306',
                    'user' => 'root',
                    'password' => '',
                    'dbname' => 'btz',
                ], //End params
            ], //End orm_default
        ], // End Connection

        'migrations_configuration' => [
            'orm_default' => [
                'directory' => 'data/Migrations',
                'name' => 'Doctrine Database Migrations',
                'namespace' => 'Migrations',
                'table' => 'migrations',
            ], //End orm_default
        ], //End migrations_configuration

    ], // End Doctrine
];