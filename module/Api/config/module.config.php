<?php
/**
 * module.config.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Api;


use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;


return [

    // Router configuration
    'router' => [
        'routes' => [
            'weixin' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/weixin[/:action[/:wxid[/:key]]][:suffix]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                        'wxid' => '[0-9]+',
                        'key' => '[a-zA-Z0-9_\-]+',
                        'suffix' => '(/|.html)',
                    ],
                    'defaults' => [
                        'controller' => Controller\WeixinController::class,
                        'action' => 'index',
                    ],
                ],
            ],

            'api' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api[/]',
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
            Controller\WeixinController::class => InvokableFactory::class,
        ],
    ],


    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],


];