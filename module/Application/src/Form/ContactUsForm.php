<?php

/**
 * Contact us form
 *
 * User: leo
 */

namespace Application\Form;



class ContactUsForm extends BaseForm
{

    /**
     * @var array
     */
    private $captchaConfig;


    /**
     * ContactUsForm constructor.
     *
     * @param array $captchaConfig
     */
    public function __construct($captchaConfig)
    {
        $this->captchaConfig = $captchaConfig;

        parent::__construct();
    }


    /**
     * 表单: 用户邮件
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
                            \Zend\Validator\NotEmpty::IS_EMPTY => '请留下您的邮件地址方便我们与您联络!',
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
                            \Zend\Validator\EmailAddress::INVALID_FORMAT => '请留下您的有效的邮件地址方便我们与您联络!',
                            \Zend\Validator\EmailAddress::INVALID_HOSTNAME => '您的邮件地址的域名很奇怪, 好像不太合适.',
                            \Zend\Validator\Hostname::LOCAL_NAME_NOT_ALLOWED => '',
                            \Zend\Validator\Hostname::INVALID_HOSTNAME => '',
                        ],
                    ],
                ],
            ],
        ]);
    }


    /**
     * 表单: 主题
     */
    private function addSubjectElement()
    {
        $this->addElement([
            'type' => 'text',
            'name' => 'subject',
            'attributes' => [
                'id' => 'subject',
            ],
            'options' => [
                'label' => 'Subject',
            ],
        ]);

        $this->addFilter([
            'name'     => 'subject',
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
                            \Zend\Validator\NotEmpty::IS_EMPTY => '麻烦您简单的给说明一下联络主题!',
                        ],
                    ],
                ],
                [
                    'name'    => 'StringLength',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'min' => 2,
                        'max' => 128,
                        'messages' => [
                            \Zend\Validator\StringLength::TOO_SHORT => '主题太简单了吧!',
                            \Zend\Validator\StringLength::TOO_LONG => '请勿在主题里填写太多内容, 详细的内容请在内容块留言.',
                        ],
                    ],
                ],
            ],
        ]);
    }


    /**
     * 表单: 内容
     */
    private function addContentElement()
    {
        $this->addElement([
            'type'  => 'textarea',
            'name' => 'message',
            'attributes' => [
                'id' => 'message'
            ],
            'options' => [
                'label' => 'Message content',
            ],
        ]);

        $this->addFilter([
            'name'     => 'message',
            'required' => true,
            'break_on_failure' => true,
            'filters'  => [
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'messages' => [
                            \Zend\Validator\NotEmpty::IS_EMPTY => '如果您真心想和我们联络, 请留下具体的内容!',
                        ],
                    ],
                ],
                [
                    'name'    => 'StringLength',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'min' => 12,
                        'max' => 4096,
                        'messages' => [
                            \Zend\Validator\StringLength::TOO_SHORT => '请再具体一点联络的内容. 太简单了我们可能无法理解您的需求.',
                            \Zend\Validator\StringLength::TOO_LONG => '您的信息太长了. 您这是在写小说呢? 我们可能看到一半就睡着了.',
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * 表单: 验证码
     */
    private function addCaptchaElement()
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
        $this->addSubjectElement();
        $this->addContentElement();
        $this->addCaptchaElement();
    }
}