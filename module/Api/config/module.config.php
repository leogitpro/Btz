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
                        'suffix' => '(.json|.html)',
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

    'api_list' => [
        'weixin' => [
            'accesstoken' => '获取公众号 access_token 接口',
            'jsapiticket' => '获取公众号 jsapi_ticket 接口',
            'apiticket' => '获取公众号卡券 api_ticket 接口',
            'userinfo' => '通过提供的 OpenID 查询用户信息接口',
            'jssign' => '公众号JS-SDK使用权限签名接口',
            'oauth' => '公众号网页授权接口',
        ],
    ],

];