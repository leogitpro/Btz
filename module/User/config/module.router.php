<?php
/**
 * Configuration module router
 *
 */

namespace User;


use Zend\Router\Http\Regex;
use Zend\Router\Http\Segment;


//Default user module url
$_route_user_default = [
    'type' => Regex::class,
    'options' => [
        'regex' => '/user(?<suffix>/|(\.html))?',
        'defaults' => [
            'controller' => Controller\AuthController::class,
            'action' => 'index',
        ],
        'spec' => '/user%suffix%',
    ],
];

//Default user auth url
$_route_user_auth = [
    'type' => Segment::class,
    'options' => [
        'route' => '/user/auth[:suffix]',
        'constraints' => [
            'suffix' => '(/|.html)',
        ],
        'defaults' => [
            'controller' => Controller\AuthController::class,
            'action' => 'index',
        ],
    ],
];

//Default user auth actions url
$_route_user_auth_actions = [
    'type' => Segment::class,
    'options' => [
        'route' => '/user/auth/:action[:suffix]',
        'constraints' => [
            'action' => '(index|login|logout|signup|active|forgot-passwd|reset-passwd)',
            'suffix' => '(/|.html)',
        ],
        'defaults' => [
            'controller' => Controller\AuthController::class,
            'action' => 'index',
        ],
    ],
];

//Default user auth active url
$_route_user_auth_active = [
    'type' => Segment::class,
    'options' => [
        'route' => '/user/auth/active/:key[:suffix]',
        'constraints' => [
            'key' => '[a-zA-Z0-9]+',
            'suffix' => '(/|.html)',
        ],
        'defaults' => [
            'controller' => Controller\AuthController::class,
            'action' => 'active',
        ],
    ],
];



//Default user profile url
$_route_user_profile = [
    'type' => Segment::class,
    'options' => [
        'route' => '/user/profile[:suffix]',
        'constraints' => [
            'suffix' => '(/|.html)',
        ],
        'defaults' => [
            'controller' => Controller\ProfileController::class,
            'action' => 'index',
        ],
    ],
];

//Default user profile actions url
$_route_user_profile_actions = [
    'type' => Segment::class,
    'options' => [
        'route' => '/user/profile/:action[:suffix]',
        'constraints' => [
            'action' => '(index|update|password|view)',
            'suffix' => '(/|.html)',
        ],
        'defaults' => [
            'controller' => Controller\ProfileController::class,
            'action' => 'index',
        ],
    ],
];

//Default user auth active url
$_route_user_profile_view = [
    'type' => Segment::class,
    'options' => [
        'route' => '/user/profile/view/:uid[:suffix]',
        'constraints' => [
            'uid' => '[0-9]+',
            'suffix' => '(/|.html)',
        ],
        'defaults' => [
            'controller' => Controller\ProfileController::class,
            'action' => 'view',
        ],
    ],
];


return [
    'routes' => [
        //Authencation routes
        'route_user_default' => $_route_user_default,
        'route_user_auth' => $_route_user_auth,
        'route_user_auth_actions' => $_route_user_auth_actions,
        'route_user_auth_active' => $_route_user_auth_active,

        //Profile routes
        'route_user_profile' => $_route_user_profile,
        'route_user_profile_actions' => $_route_user_profile_actions,
        'route_user_profile_view' => $_route_user_profile_view,
    ],
];

