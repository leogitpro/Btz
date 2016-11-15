<?php

namespace Admin;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'routes' => [
        'admin_home' => [
            'type'    => Segment::class,
            'options' => [
                'route'    => '/admin[/]',
                'defaults' => [
                    'controller' => Controller\IndexController::class,
                    'action'     => 'index',
                ],
            ],
            'may_terminate' => true,
            'child_routes' => [
                'admin_login' => [
                    'type' => Literal::class,
                    'options' => [
                        'route' => 'login.html',
                        'defaults' => [
                            'action' => 'login',
                        ],
                    ],
                ], //End admin_login
            ],
        ],
    ],
];