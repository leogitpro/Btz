<?php


use Zend\Session\Validator\RemoteAddr;
use Zend\Session\Validator\HttpUserAgent;
use Zend\Session\Storage\SessionArrayStorage;


return [
    'doctrine' => require(__DIR__ . '/doctrine.config.php'), //Global doctrine configuration.

    // Session configuration.
    'session_config' => [
        'cookie_lifetime' => 60 * 60 * 1, // Session cookie will expire in 1 hour.
        'gc_maxlifetime' => 60 * 60 * 24 * 30, // Session data stored on server time: 30days.
    ],

    // Session manager configuration.
    'session_manager' => [
        // Session validators
        'validators' => [
            RemoteAddr::class,
            HttpUserAgent::class,
        ],
    ],

    // Session storage configuration.
    'session_storage' => [
        'type' => SessionArrayStorage::class,
    ],
];