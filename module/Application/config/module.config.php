<?php
/**
 * module.config.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Application;


use Zend\ServiceManager\Factory\InvokableFactory;


return [
    'router' => [
        'routes' => require __DIR__ . '/module.config.routes.php',
    ],

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
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
            \Zend\Form\View\Helper\FormElementErrors::class => View\Helper\Factory\FormElementErrorsFactory::class,
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
            Service\AppLogger::class => Service\Factory\AppLoggerFactory::class,
            Service\DoctrineSqlLogger::class => Service\Factory\DoctrineSqlLoggerFactory::class,
        ],
        'aliases' => [
            'Logger' => Service\AppLogger::class,
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

    'logger' => [
        'writers' => [
            'default' => [
                'name' => \Zend\Log\Writer\Stream::class,
                'storage' => 'file', // Only for stream is save to file. needless for other writers.
                'options' => [
                    'stream' => rtrim(sys_get_temp_dir(), "/\\") . DIRECTORY_SEPARATOR . 'php-log-' . date('Ymd') . '.txt',
                    'mode' => 'a',
                    'log_separator' => PHP_EOL,
                    'chmod' => null,
                ],
                'filters' => [
                    'priority' => [
                        'name' => \Zend\Log\Filter\Priority::class,
                        'options' => [
                            'priority' => \Zend\Log\Logger::ERR,
                            'operator' => '<=',
                        ],
                    ],
                    /**
                    'regex' => [
                        'name' => \Zend\Log\Filter\Regex::class,
                        'options' => [
                            'regex' => '/.+/i', // Make sure execute: preg_match($regex, '') no error.
                        ],
                    ],
                    //*/
                    // ... other filter
                ],
                'formatter' => [ // Every writer only own one formatter!
                    'name' => \Zend\Log\Formatter\Simple::class,
                    'options' => [
                        'format' => '%priorityName%(%priority%) => %message% %extra%' . PHP_EOL . '%timestamp%' . PHP_EOL . PHP_EOL,
                        'dateTimeFormat' => 'Y-m-d H:i:s A D',
                    ],
                ],
            ],
            // ... other writer
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
        'template' => require __DIR__ . '/module.config.mail_tpl.php',
    ],
];
