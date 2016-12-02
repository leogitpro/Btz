<?php
/**
 * Doctrine Configurations
 *
 * need doctrine-module and doctrine-orm-module support.
 *
 * use doctrine migrations for db manage.
 */

return [
    'connection' => [  // connection configuration
        'orm_default' => [
            'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
            'params' => [
                'host' => '127.0.0.1',
                'port' => '3306',
                'user' => 'root',
                'password' => '',
                'dbname' => 'btz',
                'defaultTableOptions' => [ //For sepcial schema asset setting
                    'collate' => 'utf8mb4_unicode_ci',
                    'charset' => 'utf8mb4',
                    'engine' => 'InnoDB',
                ],
            ], //End params
        ], //End orm_default
    ], // End Connection

    'migrations_configuration' => [ //Migrations section
        'orm_default' => [
            'directory' => 'data/Migrations', // Default is data/DoctrineORMModule/Migrations
            'name' => 'Doctrine Database Migrations',
            'namespace' => 'Migrations', //Default is DoctrineORMModule\Migrations
            'table' => 'migrations', //Default is migrations
            'column'    => 'version', //Default is version
        ], //End orm_default
    ], //End migrations_configuration
];