<?php
/**
 * module.config.routes.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Application;


use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;



return [
    'home' => [
        'type' => Literal::class,
        'options' => [
            'route'    => '/',
            'defaults' => [
                'controller' => Controller\IndexController::class,
                'action'     => 'index',
            ],
        ],
    ],

    'service' => [
        'type' => Literal::class,
        'options' => [
            'route'    => '/service.html',
            'defaults' => [
                'controller' => Controller\IndexController::class,
                'action'     => 'service',
            ],
        ],
    ],

    'contact' => [
        'type' => Literal::class,
        'options' => [
            'route' => '/contact.html',
            'defaults' => [
                'controller' => Controller\IndexController::class,
                'action' => 'contact',
            ],
        ],
    ],

    'send-mail' => [
        'type' => Literal::class,
        'options' => [
            'route' => '/send-mail.html',
            'defaults' => [
                'controller' => Controller\IndexController::class,
                'action' => 'send-mail',
            ],
        ],
    ],


    'app' => [
        'type'    => Segment::class,
        'options' => [
            'route'    => '/application[/]',
            'defaults' => [
                'controller' => Controller\IndexController::class,
                'action'     => 'index',
            ],
        ],
        'may_terminate' => true,
        'child_routes' => [
            'index' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => 'index[/:action][:suffix]',
                    'constraints' => [
                        'action'     => '[a-zA-Z][a-zA-Z0-9_\-]+',
                        'suffix' => '(/|.html)',
                    ],
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
];