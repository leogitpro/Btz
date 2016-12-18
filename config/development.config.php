<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */


return [
    // Additional modules to include when in development mode
    'modules' => [
    ],
    // Configuration overrides during development mode
    'module_listener_options' => [
        'config_glob_paths' => [realpath(__DIR__) . '/autoload/{,*.}{global,local}-development.php'],
        'config_cache_enabled' => false,
        'module_map_cache_enabled' => false,
    ],


    /**
    //Doctrine configuration
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'params' => [
                    'host' => '127.0.0.1',
                    'port' => '3306',
                    'user' => 'root',
                    'password' => 'root',
                    'unix_socket' => '/Applications/MAMP/tmp/mysql/mysql.sock',
                ], //End params
            ], //End orm_default
        ], // End Connection
    ],
    //*/
];
