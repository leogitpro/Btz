<?php
/**
 * ApplyForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Application\Form;



class ApplyForm extends BaseForm
{

    /**
     * @var array
     */
    private $captchaConfig;


    public function __construct($captcha_config)
    {
        $this->captchaConfig = $captcha_config;
        parent::__construct();
    }


    /**
     * Member account
     */
    protected function addEmailElement()
    {
        $this->add([
            'type' => 'text',
            'name' => 'email',
            'attributes' => [
                'id' => 'email',
            ],
            'options' => [
                'label' => 'Your E-mail',
            ],
        ]);

        $this->getInputFilter()->add([
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
            ],
        ]);
    }


    /**
     * Member name
     */
    protected function AddNameElement()
    {
        $this->add([
            'type' => 'text',
            'name' => 'name',
            'attributes' => [
                'id' => 'name',
            ],
            'options' => [
                'label' => 'Name',
            ],
        ]);

        $this->getInputFilter()->add([
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
     * WeChat AppID
     */
    protected function addWxIdElement()
    {
        $this->add([
            'type' => 'text',
            'name' => 'appid',
            'attributes' => [
                'id' => 'appid',
            ],
            'options' => [
                'label' => 'AppID',
            ],
        ]);

        $this->getInputFilter()->add([
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
            ],
        ]);
    }


    /**
     * Form captcha
     */
    public function addCaptchaElement()
    {
        $this->add([
            'type' => 'captcha',
            'name' => 'captcha',
            'options' => [
                'label' => 'Verification code',
                'captcha' => $this->captchaConfig,
            ],
        ]);
    }



    protected function addElements()
    {
        $this->addCsrfElement();

        $this->addEmailElement();
        $this->addNameElement();
        $this->addWxIdElement();
        $this->addCaptchaElement();

        $this->addSubmitElement();
    }

}