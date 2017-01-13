<?php
/**
 * Module configuration
 */

namespace Admin;


use Admin\Controller\IndexController;
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
                            'route' => 'index[/:action[/:key]][:suffix]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'key' => '[a-zA-Z0-9]+',
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
                            'route' => 'dashboard[/:action[/:key]][:suffix]',
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
                    ], // End DashboardController router

                    // DashboardController router configuration
                    'profile' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'profile[/:action[/:key]][:suffix]',
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
                    ], // End DashboardController router

                    // DepartmentController router configuration
                    'dept' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'department[/:action[/:key]][:suffix]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'key' => '[a-zA-Z0-9_-]+',
                                'suffix' => '(/|.html)',
                            ],
                            'defaults' => [
                                'controller' => Controller\DepartmentController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                    // End DepartmentController router


                    // MemberController router configuration
                    'member' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'member[/:action[/:key]][:suffix]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'key' => '[a-zA-Z0-9_-]+',
                                'suffix' => '(/|.html)',
                            ],
                            'defaults' => [
                                'controller' => Controller\MemberController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                    // End MemberController router


                    // DepartmentMemberRelationController router configuration
                    'dmr' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'dmr[/:action[/:key]][:suffix]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'key' => '[a-zA-Z0-9]+',
                                'suffix' => '(/|.html)',
                            ],
                            'defaults' => [
                                'controller' => Controller\DepartmentMemberRelationController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                    // End MemberController router


                    // DepartmentMemberRelationController router configuration
                    'component' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'component[/:action[/:key]][:suffix]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'key' => '[a-zA-Z0-9]+',
                                'suffix' => '(/|.html)',
                            ],
                            'defaults' => [
                                'controller' => Controller\ComponentController::class,
                                'action' => 'index',
                            ],
                        ],
                    ],
                    // End MemberController router

                    // DepartmentMemberRelationController router configuration
                    'acl' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => 'acl[/:action[/:key]][:suffix]',
                            'constraints' => [
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                                'key' => '[a-zA-Z0-9]+',
                                'suffix' => '(/|.html)',
                            ],
                            'defaults' => [
                                'controller' => Controller\AclController::class,
                                'action' => 'member',
                            ],
                        ],
                    ],
                    // End MemberController router

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
            Controller\DepartmentController::class => InvokableFactory::class,
            Controller\MemberController::class => InvokableFactory::class,
            Controller\DepartmentMemberRelationController::class => InvokableFactory::class,
            Controller\ComponentController::class => InvokableFactory::class,
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
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],

    'view_helpers' => [
        'factories' => [
            View\Helper\TopRightMenu::class => View\Helper\Factory\MenuFactory::class,
            View\Helper\SideTreeMenu::class => View\Helper\Factory\MenuFactory::class,
            View\Helper\PageTitleBar::class => InvokableFactory::class,
            View\Helper\PageBreadcrumbBar::class => View\Helper\Factory\MenuFactory::class,
            View\Helper\Pagination::class => InvokableFactory::class,
        ],
        'aliases' => [
            'topRightMenu' => View\Helper\TopRightMenu::class,
            'sideTreeMenu' => View\Helper\SideTreeMenu::class,
            'pageTitleBar' => View\Helper\PageTitleBar::class,
            'pageBreadcrumbBar' => View\Helper\PageBreadcrumbBar::class,
            'pagination' => View\Helper\Pagination::class,
        ],
    ],


    // Service manager configuration
    'service_manager' => [
        'factories' => [
            Service\AuthAdapter::class => Service\Factory\AuthAdapterFactory::class,
            Service\AuthService::class => Service\Factory\AuthServiceFactory::class,
            Service\AuthManager::class => Service\Factory\AuthManagerFactory::class,
            Service\NavManager::class => Service\Factory\NavManagerFactory::class,
            Service\MemberManager::class => Service\Factory\MemberManagerFactory::class,
            Service\DepartmentManager::class => Service\Factory\EntityManagerFactory::class,
            Service\DepartmentMemberRelationManager::class => Service\Factory\DMRelationManagerFactory::class,
            Service\ComponentManager::class => Service\Factory\EntityManagerFactory::class,
            Service\AclManager::class => Service\Factory\EntityManagerFactory::class,
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