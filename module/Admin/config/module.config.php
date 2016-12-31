<?php
/**
 * Module configuration
 */

namespace Admin;


use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;


return [

    // Router configuration
    'router' => [
        'routes' => [
            'admin' => [
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

                    // IndexController router configuration
                    'index' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'index[/:action][:suffix]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'suffix' => '(/|.html)',
                            ],
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action' => 'index',
                            ],
                        ],
                    ], // End IndexController router

                    // DashboardController router configuration
                    'dashboard' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'dashboard[/:action][:suffix]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'suffix' => '(/|.html)',
                            ],
                            'defaults' => [
                                'controller' => Controller\DashboardController::class,
                                'action' => 'index',
                            ],
                        ],
                    ], // End DashboardController router


                    'dashboard_detail' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'dashboard/:action/:key[:suffix]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'key' => '[a-zA-Z0-9]+',
                                'suffix' => '(/|.html)',
                            ],
                            'defaults' => [
                                'controller' => Controller\DashboardController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],

                    // DashboardController router configuration
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
                    ], // End DashboardController router

                ],
            ],
        ],
    ],

    // Controller configuration
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\DashboardController::class => InvokableFactory::class,
            Controller\ProfileController::class => InvokableFactory::class,
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            Controller\Plugin\MessagePlugin::class => InvokableFactory::class,
        ],
        'aliases' => [
            'getMessagePlugin' => Controller\Plugin\MessagePlugin::class,
        ],
    ],


    // View configuration
    'view_manager' => [
        'template_map' => [
            'layout/admin_simple'  => __DIR__ . '/../view/layout/simple.phtml',
            'layout/admin_layout' => __DIR__ . '/../view/layout/layout.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

    'view_helpers' => [
        'factories' => [
            View\Helper\TopRightMenu::class => View\Helper\Factory\MenuFactory::class,
            View\Helper\SideTreeMenu::class => View\Helper\Factory\MenuFactory::class,
            View\Helper\PageTitleBar::class => InvokableFactory::class,
            View\Helper\PageBreadcrumbBar::class => View\Helper\Factory\MenuFactory::class,
        ],
        'aliases' => [
            'topRightMenu' => View\Helper\TopRightMenu::class,
            'sideTreeMenu' => View\Helper\SideTreeMenu::class,
            'pageTitleBar' => View\Helper\PageTitleBar::class,
            'pageBreadcrumbBar' => View\Helper\PageBreadcrumbBar::class,
        ],
    ],


    // Service manager configuration
    'service_manager' => [
        'factories' => [
            Service\AuthAdapter::class => Service\Factory\AuthAdapterFactory::class,
            Service\AdminerManager::class => Service\Factory\EntityManagerFactory::class,
            Service\AuthService::class => Service\Factory\AuthServiceFactory::class,
            Service\AuthManager::class => Service\Factory\AuthManagerFactory::class,
            Service\NavManager::class => Service\Factory\NavManagerFactory::class,
        ],
    ],


    // Doctrine entity configuration
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
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

];