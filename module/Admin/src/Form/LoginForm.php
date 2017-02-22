<?php
/**
 * Administrator login form generator
 */

namespace Admin\Form;


use Zend\Form\Form;
use Zend\InputFilter\InputFilter;


class LoginForm extends Form
{

    public function __construct()
    {
        parent::__construct('login_form');

        $this->setAttributes(['method' => 'post', 'role' => 'form']);

        $this->setInputFilter(new InputFilter());

        $this->addElements();
        $this->addInputFilters();
    }


    /**
     * Add the form fields
     */
    public function addElements()
    {
        // CSRF field
        $this->add([
            'type'  => 'csrf',
            'name' => 'csrf',
            'attributes' => [],
            'options' => [
                'csrf_options' => [
                    'timeout' => 600
                ]
            ],
        ]);

        // Email account field
        $this->add([
            'type' => 'text',
            'name' => 'email',
            'attributes' => [
                'id' => 'email',
            ],
            'options' => [
                'label' => '登录账户(E-mail)',
            ],
        ]);

        // Password field
        $this->add([
            'type' => 'password',
            'name' => 'password',
            'attributes' => [
                'id' => 'password',
            ],
            'options' => [
                'label' => '登录密码',
            ],
        ]);

        // Submit field
        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'id' => 'submit',
                'value' => '现在登入',
            ],
        ]);
    }


    /**
     * Add the form field filters and validators
     */
    public function addInputFilters()
    {

        // E-mail filter and validators
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

        // Password field filters and validators
        $this->getInputFilter()->add([
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

}