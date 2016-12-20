<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;


return [
    'router' => [
        'routes' => [
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
                    'auto_segment' => [
                        'type'    => Segment::class,
                        'options' => [
                            'route'    => '[:controller[/:action]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]+',
                            ],
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
        ],
        'aliases' => [
            'index' => Controller\IndexController::class,
            'user' => Controller\UserController::class,
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            Controller\Plugin\AccessPlugin::class => InvokableFactory::class,
        ],
        'aliases' => [
            'access' => Controller\Plugin\AccessPlugin::class,
        ],
    ],

    // View configuration.
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

    'view_helpers' => [
        'factories' => [
            View\Helper\Menu::class => View\Helper\Factory\MenuFactory::class,
            View\Helper\Breadcrumbs::class => InvokableFactory::class,
        ],
        'aliases' => [
            'barMenu' => View\Helper\Menu::class,
            'barBreadcrumbs' => View\Helper\Breadcrumbs::class,
        ],
    ],

    'service_manager' => [
        'factories' => [
            Log\AppLogger::class => Log\AppLoggerFactory::class,
            Service\MailManager::class => Service\Factory\MailManagerFactory::class,
            Service\NavManager::class => Service\Factory\NavManagerFactory::class,
        ],
        'aliases' => [
            'AppLogger' => Log\AppLogger::class,
        ],

    ],

    // App logger configuration
    'applogger' => require(__DIR__ . '/config.applogger.php'),

    // Mail service configuration
    'mail' => [
        'smtp' => [
            'name' => 'MailService',
            'host' => '',
            'port' => 465,
            'connection_class' => 'login',
            'connection_config' => [
                'username' => '',
                'password' => '',
                'ssl' => 'ssl',
            ],
        ],
    ],
];
