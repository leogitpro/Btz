<?php
/**
 * module.routes.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Api;


use Zend\Router\Http\Segment;


return [
    // IndexController router configuration
    'index' => [
        'type' => Segment::class,
        'options' => [
            'route' => 'index[/:action[/:key]][:suffix]',
            'constraints' => [
                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                'key' => '[a-zA-Z0-9_\-]+',
                'suffix' => '(/|.html)',
            ],
            'defaults' => [
                'controller' => Controller\IndexController::class,
                'action' => 'index',
            ],
        ],
    ], // End IndexController router

    // WechatController router configuration
    'wechat' => [
        'type' => Segment::class,
        'options' => [
            'route' => 'wechat[/:action[/:key]][:suffix]',
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
    ], // End WechatController router
];