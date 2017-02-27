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
            'wxapi' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/wx[/:action[/:key]][:suffix]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                        'key' => '[a-zA-Z0-9_\-]+',
                        'suffix' => '(/|.html)',
                    ],
                    'defaults' => [
                        'controller' => Controller\WechatController::class,
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
            Controller\WechatController::class => InvokableFactory::class,
            Controller\QrcodeController::class => InvokableFactory::class,
        ],
    ],

];