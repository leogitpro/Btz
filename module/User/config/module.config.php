<?php
/**
 * Module configuration
 */

namespace User;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'router' => require(__DIR__ . '/module.router.php'),
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
