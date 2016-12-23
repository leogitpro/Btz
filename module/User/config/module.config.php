<?php
/**
 * Module configuration
 */

namespace User;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'user' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/user[/]',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'auth' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'auth[/:action][:suffix]',
                            'constraints' => [
                                //'action' => '(index|login|logout|sign-up|activated|active|forgot-password|reset-password)',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'suffix' => '(/|.html)',
                            ],
                            'defaults' => [
                                'controller' => Controller\AuthController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'auth_detail' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'auth/:action/:key[:suffix]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'key' => '[a-zA-Z0-9]+',
                                'suffix' => '(/|.html)',
                            ],
                            'defaults' => [
                                'controller' => Controller\AuthController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],

                    'profile' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'profile[/:action][:suffix]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'suffix' => '(/|.html)',
                            ],
                            'defaults' => [
                                'controller' => Controller\ProfileController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                    'profile_detail' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'profile/:action/:key[:suffix]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'key' => '[a-zA-Z0-9]+',
                                'suffix' => '(/|.html)',
                            ],
                            'defaults' => [
                                'controller' => Controller\ProfileController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                ],
            ],
        ]
    ],


    'controllers' => require(__DIR__ . '/module.controller.php'),
    'view_manager' => require(__DIR__ . '/module.view.php'),
    'service_manager' => [
        'factories' => [
            \Zend\Authentication\AuthenticationService::class => Service\Factory\AuthenticationServiceFactory::class,
            Service\UserManager::class => Service\Factory\UserManagerFactory::class,
            Service\AuthManager::class => Service\Factory\AuthManagerFactory::class,
            Service\AuthAdapter::class => Service\Factory\AuthAdapterFactory::class,
        ],
    ],

    // Doctrine entity configuration
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver',
                ],
            ],
        ],
    ],

    // User mail service configuration
    'mail' => [
        'template' => require(__DIR__ . '/module.config.mail_tpl.php'),
    ],
];
