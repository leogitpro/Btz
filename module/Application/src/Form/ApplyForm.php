<?php
/**
 * ApplyForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Application\Form;



use Admin\Service\MemberManager;
use Admin\Validator\MemberEmailUniqueValidator;
use WeChat\Service\AccountService;
use WeChat\Validator\AppIdUniqueValidator;


class ApplyForm extends BaseForm
{

    /**
     * @var array
     */
    private $captchaConfig;

    /**
     * @var MemberManager
     */
    private $memberManager;

    /**
     * @var AccountService
     */
    private $accountService;


    public function __construct(MemberManager $memberManager, AccountService $accountService, $captcha_config)
    {
        $this->captchaConfig = $captcha_config;
        $this->memberManager = $memberManager;
        $this->accountService = $accountService;

        parent::__construct();
    }


    /**
     * 表单: 用户账号
     */
    private function addEmailElement()
    {
        $this->addElement([
            'type' => 'text',
            'name' => 'email',
            'attributes' => [
                'id' => 'email',
            ],
            'options' => [
                'label' => 'Your E-mail',
            ],
        ]);

        $this->addFilter([
            'name' => 'email',
            'required' => true,
            'break_on_failure' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'messages' => [
                            \Zend\Validator\NotEmpty::IS_EMPTY => '请填写邮箱地址!',
                        ],
                    ],
                ],
                [
                    'name' => 'EmailAddress',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'allow' => \Zend\Validator\Hostname::ALLOW_DNS,
                        'useMxCheck' => false,
                        'messages' => [
                            \Zend\Validator\EmailAddress::INVALID_HOSTNAME => '您的邮件地址的域名很奇怪, 好像不太合适.',
                            \Zend\Validator\Hostname::LOCAL_NAME_NOT_ALLOWED => '',
                            \Zend\Validator\Hostname::INVALID_HOSTNAME => '',
                            \Zend\Validator\EmailAddress::INVALID_FORMAT => '请留下您的有效的邮件地址接收试用激活码!',
                        ],
                    ],
                ],
                [
                    'name' => MemberEmailUniqueValidator::class,
                    'break_chain_on_failure' => true,
                    'options' => [
                        'memberManager' => $this->memberManager,
                        'messages' => [
                            MemberEmailUniqueValidator::EMAIL_EXISTED => '该邮件地址已经申请过, 不可重复申请试用.',
                        ],
                    ],

                ],
            ],
        ]);
    }


    /**
     * 表单: 用户名称
     */
    private function AddNameElement()
    {
        $this->addElement([
            'type' => 'text',
            'name' => 'name',
            'attributes' => [
                'id' => 'name',
            ],
            'options' => [
                'label' => 'Name',
            ],
        ]);

        $this->addFilter([
            'name'     => 'name',
            'required' => true,
            'break_on_failure' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'StripNewlines'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'messages' => [
                            \Zend\Validator\NotEmpty::IS_EMPTY => '请告诉我们您的名字!',
                        ],
                    ],
                ],
                [
                    'name'    => 'StringLength',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'min' => 1,
                        'max' => 15,
                        'messages' => [
                            \Zend\Validator\StringLength::TOO_SHORT => '您的名字太短了!',
                            \Zend\Validator\StringLength::TOO_LONG => '请使用一个正常的名字!',
                        ],
                    ],
                ],
            ],
        ]);
    }


    /**
     * 表单: 微信 AppID
     */
    private function addWeChatAppIdElement()
    {
        $this->addElement([
            'type' => 'text',
            'name' => 'appid',
            'attributes' => [
                'id' => 'appid',
            ],
            'options' => [
                'label' => 'AppID',
            ],
        ]);

        $this->addFilter([
            'name'     => 'appid',
            'required' => true,
            'break_on_failure' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'StripNewlines'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'messages' => [
                            \Zend\Validator\NotEmpty::IS_EMPTY => '请填写微信公众号 AppID!',
                        ],
                    ],
                ],
                [
                    'name'    => 'Regex',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'pattern' => "/^wx[0-9a-z]+$/",
                        'messages' => [
                            \Zend\Validator\Regex::NOT_MATCH=> '请填写正确的 AppID, 请注意大小写!',
                        ],
                    ],
                ],
                [
                    'name' => AppIdUniqueValidator::class,
                    'break_chain_on_failure' => true,
                    'options' => [
                        'accountService' => $this->accountService,
                        'messages' => [
                            AppIdUniqueValidator::APPID_EXISTED => '该公众号已经申请过, 不可重复申请试用.',
                        ],
                    ],

                ],
            ],
        ]);
    }


    /**
     * 表单: 微信 AppSecret
     */
    private function addWeChatAppSecretElement()
    {
        $this->addElement([
            'type' => 'text',
            'name' => 'appsecret',
            'attributes' => [
                'id' => 'appsecret',
            ],
            'options' => [
                'label' => 'AppSecret',
            ],
        ]);

        $this->addFilter([
            'name'     => 'appsecret',
            'required' => true,
            'break_on_failure' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'StripNewlines'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'messages' => [
                            \Zend\Validator\NotEmpty::IS_EMPTY => '请填写微信公众号 AppSecret!',
                        ],
                    ],
                ],
                [
                    'name'    => 'Regex',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'pattern' => "/^[0-9a-zA-Z]+$/",
                        'messages' => [
                            \Zend\Validator\Regex::NOT_MATCH=> '请填写正确的 AppSecret!',
                        ],
                    ],
                ],
            ],
        ]);
    }


    /**
     * 表单: 验证码
     */
    public function addCaptchaElement()
    {
        $this->addElement([
            'type' => 'captcha',
            'name' => 'captcha',
            'options' => [
                'label' => 'Verification code',
                'captcha' => $this->captchaConfig,
            ],
        ]);

        $this->addFilter([
            'name'     => 'captcha',
            'break_on_failure' => true,
            'validators' => [
                [
                    'name' => \Zend\Captcha\Image::class,
                    'options' => [
                        'messages' => [
                            \Zend\Captcha\Image::BAD_CAPTCHA => '请输入正确的验证码!',
                        ],
                    ],
                ],
            ],
        ]);
    }


    public function addElements()
    {
        $this->addEmailElement();
        $this->AddNameElement();
        $this->addWeChatAppIdElement();
        $this->addWeChatAppSecretElement();
        $this->addCaptchaElement();
    }

}