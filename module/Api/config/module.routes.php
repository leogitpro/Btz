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
    ],

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

];