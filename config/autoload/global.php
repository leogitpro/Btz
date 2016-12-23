<?php


use Zend\Session\Validator\RemoteAddr;
use Zend\Session\Validator\HttpUserAgent;
use Zend\Session\Storage\SessionArrayStorage;


return [
    'doctrine' => require(__DIR__ . '/doctrine.config.php'), //Global doctrine configuration.

    // Captcha configuration
    'captcha' => [
        'class' => 'image',
        'font' => __DIR__ .'/../../data/font/Moms_typewriter.ttf', // Font path
        'imgDir' => __DIR__ . '/../../public/img/captcha', // Captcha image save path
        'imgUrl' => '/img/captcha/', // Image access path
        'suffix' => '.png', // Image suffix
        'fontSize' => 34,
        'wordlen' => 8, // Chars count
        'width' => 300, // Image width
        'height' => 80, // Image height
        'expiration' => 600, // Expire time 10 min.
        'lineNoiseLevel' => 1, // The noise lines number.
        'dotNoiseLevel' => 40, // The noise point number.
    ],

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