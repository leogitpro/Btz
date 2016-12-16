<?php
/**
 * Module configuration
 */

namespace User;

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
];