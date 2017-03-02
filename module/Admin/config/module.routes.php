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

    // DashboardController router configuration
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
    ], // End DashboardController router

    // DashboardController router configuration
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
    ], // End DashboardController router

    // DashboardController router configuration
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
    ], // End DashboardController router

    // DepartmentController router configuration
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
    // End DepartmentController router


    // MemberController router configuration
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
    // End MemberController router


    // ComponentController router configuration
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
    // End ComponentController router

    // AclController router configuration
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
    // End AclController router


    // MessageController router configuration
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
    // End MessageController router

    // FeedbackController router configuration
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
    // End FeedbackController router


    // WeChatController router configuration
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
    // End WeChatController router


    // WeChatController router configuration
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
    // End WeChatController router

    // WeChatQrCodeController router configuration
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
    // End WeChatQrCodeController router

];