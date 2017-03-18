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

    'controllers' => [
        'factories' => [
            Controller\IndexController::class => InvokableFactory::class,
        ],
    ],


    'service_manager' => [
        'factories' => [
            Service\NetworkManager::class => Service\Factory\BaseFactory::class,
        ],
    ],
];