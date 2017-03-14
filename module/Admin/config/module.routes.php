<?php
/**
 * module.routes.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin;


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

    'dashboard' => [
        'type' => Segment::class,
        'options' => [
            'route' => 'dashboard[/:action[/:key]][:suffix]',
            'constraints' => [
                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                'key' => '[a-zA-Z0-9_\-]+',
                'suffix' => '(/|.html)',
            ],
            'defaults' => [
                'controller' => Controller\DashboardController::class,
                'action' => 'index',
            ],
        ],
    ],

    'search' => [
        'type' => Segment::class,
        'options' => [
            'route' => 'search[/:action[/:key]][:suffix]',
            'constraints' => [
                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                'key' => '[a-zA-Z0-9_\-]+',
                'suffix' => '(/|.html)',
            ],
            'defaults' => [
                'controller' => Controller\SearchController::class,
                'action' => 'index',
            ],
        ],
    ],

    'profile' => [
        'type' => Segment::class,
        'options' => [
            'route' => 'profile[/:action[/:key]][:suffix]',
            'constraints' => [
                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                'key' => '[a-zA-Z0-9_\-]+',
                'suffix' => '(/|.html)',
            ],
            'defaults' => [
                'controller' => Controller\ProfileController::class,
                'action' => 'index',
            ],
        ],
    ],

    'dept' => [
        'type' => Segment::class,
        'options' => [
            'route' => 'department[/:action[/:key]][:suffix]',
            'constraints' => [
                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                'key' => '[a-zA-Z0-9_\-]+',
                'suffix' => '(/|.html)',
            ],
            'defaults' => [
                'controller' => Controller\DepartmentController::class,
                'action' => 'index',
            ],
        ],
    ],

    'member' => [
        'type' => Segment::class,
        'options' => [
            'route' => 'member[/:action[/:key]][:suffix]',
            'constraints' => [
                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                'key' => '[a-zA-Z0-9_\-]+',
                'suffix' => '(/|.html)',
            ],
            'defaults' => [
                'controller' => Controller\MemberController::class,
                'action' => 'index',
            ],
        ],
    ],

    'component' => [
        'type' => Segment::class,
        'options' => [
            'route' => 'component[/:action[/:key]][:suffix]',
            'constraints' => [
                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                'key' => '[a-zA-Z0-9_\-\%]+',
                'suffix' => '(/|.html)',
            ],
            'defaults' => [
                'controller' => Controller\ComponentController::class,
                'action' => 'index',
            ],
        ],
    ],

    'acl' => [
        'type' => Segment::class,
        'options' => [
            'route' => 'acl[/:action[/:key]][:suffix]',
            'constraints' => [
                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                'key' => '[a-zA-Z0-9_\-]+',
                'suffix' => '(/|.html)',
            ],
            'defaults' => [
                'controller' => Controller\AclController::class,
                'action' => 'index',
            ],
        ],
    ],

    'message' => [
        'type' => Segment::class,
        'options' => [
            'route' => 'message[/:action[/:key]][:suffix]',
            'constraints' => [
                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                'key' => '[a-zA-Z0-9_\-]+',
                'suffix' => '(/|.html)',
            ],
            'defaults' => [
                'controller' => Controller\MessageController::class,
                'action' => 'index',
            ],
        ],
    ],

    'feedback' => [
        'type' => Segment::class,
        'options' => [
            'route' => 'feedback[/:action[/:key]][:suffix]',
            'constraints' => [
                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                'key' => '[a-zA-Z0-9_\-]+',
                'suffix' => '(/|.html)',
            ],
            'defaults' => [
                'controller' => Controller\FeedbackController::class,
                'action' => 'index',
            ],
        ],
    ],

    'weChat' => [
        'type' => Segment::class,
        'options' => [
            'route' => 'we-chat[/:action[/:key]][:suffix]',
            'constraints' => [
                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                'key' => '[a-zA-Z0-9_\-]+',
                'suffix' => '(/|.html)',
            ],
            'defaults' => [
                'controller' => Controller\WeChatController::class,
                'action' => 'index',
            ],
        ],
    ],

    'weChatAccount' => [
        'type' => Segment::class,
        'options' => [
            'route' => 'we-chat-account[/:action[/:key]][:suffix]',
            'constraints' => [
                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                'key' => '[a-zA-Z0-9_\-]+',
                'suffix' => '(/|.html)',
            ],
            'defaults' => [
                'controller' => Controller\WeChatAccountController::class,
                'action' => 'index',
            ],
        ],
    ],

    'weChatClient' => [
        'type' => Segment::class,
        'options' => [
            'route' => 'we-chat-client[/:action[/:key]][:suffix]',
            'constraints' => [
                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                'key' => '[a-zA-Z0-9_\-]+',
                'suffix' => '(/|.html)',
            ],
            'defaults' => [
                'controller' => Controller\WeChatClientController::class,
                'action' => 'index',
            ],
        ],
    ],

    'weChatMenu' => [
        'type' => Segment::class,
        'options' => [
            'route' => 'we-chat-menu[/:action[/:key]][:suffix]',
            'constraints' => [
                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                'key' => '[a-zA-Z0-9_\-]+',
                'suffix' => '(/|.html)',
            ],
            'defaults' => [
                'controller' => Controller\WeChatMenuController::class,
                'action' => 'index',
            ],
        ],
    ],

    'weChatQrCode' => [
        'type' => Segment::class,
        'options' => [
            'route' => 'we-chat-qr-code[/:action[/:key]][:suffix]',
            'constraints' => [
                'action' => '[a-zA-Z][a-zA-Z0-9_\-]+',
                'key' => '[a-zA-Z0-9_\-]+',
                'suffix' => '(/|.html)',
            ],
            'defaults' => [
                'controller' => Controller\WeChatQrCodeController::class,
                'action' => 'index',
            ],
        ],
    ],

];