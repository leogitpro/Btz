<?php
/**
 * module.config.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace WeChat;


use Zend\ServiceManager\Factory\InvokableFactory;


return [

    'router' => [
        'routes' => [
            'wx' => [
                'type'    => \Zend\Router\Http\Segment::class,
                'options' => [
                    'route'    => '/wx[/]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type'    => \Zend\Router\Http\Segment::class,
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
        ],
    ],

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

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
        ],
    ],


    'service_manager' => [
        'factories' => [
            Service\AccountService::class => Service\Factory\BaseEntityFactory::class,
            Service\TagService::class => Service\Factory\BaseEntityFactory::class,
            Service\ClientService::class => Service\Factory\BaseEntityFactory::class,
            Service\QrCodeService::class => Service\Factory\BaseEntityFactory::class,
            Service\WeChatService::class => Service\Factory\WeChatFactory::class,
        ],
    ],
];