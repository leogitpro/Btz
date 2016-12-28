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
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]+',
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
        ],
    ],

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,
            Controller\DisplayController::class => InvokableFactory::class,
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            Controller\Plugin\ServerPlugin::class => InvokableFactory::class,
            Controller\Plugin\DisplayPlugin::class => InvokableFactory::class,
            Controller\Plugin\AsyncRequestPlugin::class => InvokableFactory::class,
            Controller\Plugin\ConfigPlugin::class => Controller\Plugin\Factory\ConfigPluginFactory::class,
            Controller\Plugin\LoggerPlugin::class => Controller\Plugin\Factory\LoggerPluginFactory::class,
        ],
        'aliases' => [
            'getServerPlugin' => Controller\Plugin\ServerPlugin::class,
            'getDisplayPlugin' => Controller\Plugin\DisplayPlugin::class,
            'getAsyncRequestPlugin' => Controller\Plugin\AsyncRequestPlugin::class,
            'getConfigPlugin' => Controller\Plugin\ConfigPlugin::class,
            'getLoggerPlugin' => Controller\Plugin\LoggerPlugin::class,
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
            Service\ContactManager::class => Service\Factory\EntityManagerFactory::class,
            Service\MailManager::class => Service\Factory\MailManagerFactory::class,
            Service\NavManager::class => Service\Factory\NavManagerFactory::class,
        ],
        'aliases' => [
            'Logger' => 'AppLogger', // The name: Logger is the key: $config['log']['AppLogger'].
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

    // Logger configuration
    'log' => [
        'AppLogger' => [
            'writers' => [
                [
                    'name' => 'stream',
                    'priority' => \Zend\Log\Logger::DEBUG,
                    'options' => [
                        'stream' => rtrim(sys_get_temp_dir(), "/\\") . DIRECTORY_SEPARATOR . 'php-log-' . date('Ymd') . '.txt',
                        'formatter' => [
                            'name' => 'simple',
                            'options' => [
                                'format' => '%priorityName%(%priority%) => %message% %extra%' . PHP_EOL . '%timestamp%' . PHP_EOL . PHP_EOL,
                                'dateTimeFormat' => 'Y-m-d H:i:s A D',
                            ],
                        ],
                        'filters' => [
                            /**
                            [
                                'name' => 'priority',
                                'options' => [
                                    'priority' => \Zend\Log\Logger::DEBUG,
                                ],
                            ],
                            //*/
                            /**
                            [
                                'name' => 'regex',
                                'options' => [
                                    'regex' => '/test/i', // Log message content matched `test` string.
                                ],
                            ],
                            //*/
                        ],
                    ],
                ],
            ],
        ],
    ],

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
        'contact' => 'name@example.com',
        'template' => [
            'contact' => '
Hi:
    Master!
    
There is a new contact from the E-mail: %email%.

%message%

Message post time: %datetime%.
Thanks!
            ',
        ],
    ],

    // Public actions access configuration
    'access_filter' => [
        'controllers' => [
            Controller\IndexController::class => ['*'], // All action can ben access for unauthenticated user.
            Controller\DisplayController::class => ['*'], // Same as the previous.
        ],
    ],

];
