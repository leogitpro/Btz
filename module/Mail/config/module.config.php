<?php
/**
 * module.config.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Mail;


$_TPL_CONTACT = <<<EOF
Hi:
    主人大大!
    
我们收到一个客户的联络信息.
================================================================

%message%

================================================================

联络者邮箱: %email%

以上!
EOF;



return [

    'service_manager' => [
        'factories' => [
            Service\MailService::class => Service\Factory\MailFactory::class,
        ],
        'aliases' => [
            'Mail' => Service\MailService::class,
        ],
    ],

    'mail' => [
        'smtp' => [
            'name' => 'MailService',
            'host' => '',
            'port' => 465,
            'connection_class' => 'login',
            'connection_config' => [
                'username' => '',
                'password' => '',
                'ssl' => 'ssl',
            ],
        ],
        'contact' => 'name@example.com',
        'template' => [
            'contact' => $_TPL_CONTACT,
        ],
    ],
];