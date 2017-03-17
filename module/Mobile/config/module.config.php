<?php
/**
 * module.config.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Mobile;


use Zend\ServiceManager\Factory\InvokableFactory;


return [

    'router' => [
        'routes' => [
            'mobile' => [
                'type'    => \Zend\Router\Http\Segment::class,
                'options' => [
                    'route'    => '/mobile[/]',
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

    'view_manager' => [
        'template_map' => [
            'layout/mobile' => __DIR__ . '/../view/layout/layout.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];