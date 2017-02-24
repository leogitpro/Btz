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
                'child_routes' => require(__DIR__ . '/module.routes.php'),
            ],
        ],
    ],

    // Controller configuration
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
            Controller\DashboardController::class => InvokableFactory::class,
            Controller\ProfileController::class => InvokableFactory::class,
            Controller\SearchController::class => InvokableFactory::class,

            Controller\DepartmentController::class => InvokableFactory::class,
            Controller\MemberController::class => InvokableFactory::class,
            Controller\ComponentController::class => InvokableFactory::class,
            Controller\AclController::class => InvokableFactory::class,
            Controller\MessageController::class => InvokableFactory::class,
            Controller\FeedbackController::class => InvokableFactory::class,
            Controller\WechatController::class => InvokableFactory::class,
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
            Service\AuthService::class => Service\Factory\AuthServiceFactory::class,
            Service\AuthManager::class => Service\Factory\AuthManagerFactory::class,
            Service\NavManager::class => Service\Factory\NavManagerFactory::class,

            Service\MemberManager::class => Service\Factory\MemberManagerFactory::class,
            Service\DepartmentManager::class => Service\Factory\EntityManagerFactory::class,

            Service\AuthAdapter::class => Service\Factory\EntityManagerFactory::class,
            Service\ComponentManager::class => Service\Factory\EntityManagerFactory::class,
            Service\FeedbackManager::class => Service\Factory\EntityManagerFactory::class,
            Service\WechatManager::class => Service\Factory\EntityManagerFactory::class,

            Service\MessageManager::class => Service\Factory\MessageManagerFactory::class,

            Service\AclManager::class => Service\Factory\AclManagerFactory::class,

            Wechat\Service::class => Wechat\Factory\ServiceFactory::class,
            Wechat\Local::class => Wechat\Factory\LocalFactory::class,
            Wechat\Remote::class => Wechat\Factory\RemoteFactory::class,
            Wechat\Http::class => Wechat\Factory\BaseFactory::class,

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