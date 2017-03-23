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

$_TPL_APPLY = <<<EOF
您好, %username%!

欢迎您使用微信接口管理平台. 我们已经验证过您的公众号的信息并且已经为您开通了相关服务.
请激活你的账号使用密码: %password% 登录平台.

请在访问下面的地址直接进行账号激活.
%active_url%

如果你的邮件不支持点击跳转. 请复制上面的 URL 到浏览器的地址栏里打开.

您的管理平台账号有效到: %account_expired% 
您的公众号体验期截止到: %wechat_expired%

您有任何问题可以在管理中心给我们提出建议. 我们会第一时间进行回应.

友情提醒: 本邮件为系统自动发送. 请勿回复此邮件.
如您需要和我们联系, 请从在本页直接和我们联络: %contact_url%

再次感谢!

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
            'apply' => $_TPL_APPLY,
        ],
    ],
];