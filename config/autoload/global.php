<?php

return [

    // Doctrine configuration
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

    //**
    // Session configuration.
    'session_config' => [
        'cookie_lifetime' => 3600, // Session cookie will expire in 1 hour.
        'gc_maxlifetime' => 60 * 60 * 24 * 30, // Session data stored on server time: 30days.

        /** Default php config
        'save_handler' => 'files',
        'save_path' => '/tmp',
        //*/

        //**
        // Redis support need install PHP Redis extension
        'save_handler' => 'redis',
        'save_path' => 'tcp://192.168.30.81:6379?weight=1&timeout=1',
        //*/
    ],

    'session_manager' => [ // Session manager configuration.
        'validators' => [
            \Zend\Session\Validator\RemoteAddr::class,
            //\Zend\Session\Validator\HttpUserAgent::class,
        ],
    ],

    'session_storage' => [ // Session storage configuration.
        'type' => \Zend\Session\Storage\SessionArrayStorage::class,
    ],
    //*/
];