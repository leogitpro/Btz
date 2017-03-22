<?php
/**
 * Member login form generator
 */

namespace Admin\Form;


class LoginForm extends BaseForm
{

    /**
     * 表单: 登录帐号
     */
    private function addAccountElement()
    {
        $this->addElement([
            'type' => 'text',
            'name' => 'email',
            'attributes' => [
                'id' => 'email',
            ],
            'options' => [
                'label' => '登录账户(E-mail)',
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
                            \Zend\Validator\NotEmpty::IS_EMPTY => '登入账号不能为空哦!',
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
                            \Zend\Validator\EmailAddress::INVALID_FORMAT => '您的账号格式是不是输错了哦!',
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * 表单: 登录密码
     */
    private function addPasswordElement()
    {
        $this->addElement([
            'type' => 'password',
            'name' => 'password',
            'attributes' => [
                'id' => 'password',
            ],
            'options' => [
                'label' => '登录密码',
            ],
        ]);

        $this->addFilter([
            'name'     => 'password',
            'required' => true,
            'break_on_failure' => true,
            'filters'  => [
                [
                    'name' => 'StringToLower',
                    'options' => [
                        'encoding' => 'UTF-8',
                    ],
                ],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'messages' => [
                            \Zend\Validator\NotEmpty::IS_EMPTY => '登入密码不能为空哦!',
                        ],
                    ],
                ],
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 4,
                        'max' => 20,
                        'messages' => [
                            \Zend\Validator\StringLength::TOO_SHORT => '密码太短了, 最少需要4个字符哦!',
                            \Zend\Validator\StringLength::TOO_LONG => '你输入的密码太长了点. 能记住的都是大神!',
                        ],
                    ],
                ],
            ],
        ]);
    }


    public function addElements()
    {
        $this->addAccountElement();
        $this->addPasswordElement();
    }
}