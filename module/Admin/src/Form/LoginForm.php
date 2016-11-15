<?php


namespace Admin\Form;



use Zend\Form\Form;

use Zend\Form\Element\Text;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;

use Zend\Filter\FilterChain;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;

use Zend\Validator\ValidatorChain;
use Zend\Validator\Hostname;
use Zend\Validator\EmailAddress;
use Zend\Validator\StringLength;


class LoginForm extends Form
{

    public function __construct()
    {
        parent::__construct('login-form');

        $this->setAttributes([
            'method' => 'post',
            'role' => 'form',
        ]);

        $this->setInputFilter(new InputFilter());

        $this->addElements();

    }


    private function addAccount()
    {
        $inputName = 'email';

        //工厂模式生成
        /**
        $email = [
        'type' => 'text',
        'name' => $inputName,
        'attributes' => [
        'id' => $inputName,
        'class' => 'login-input',
        ],
        'options' => [
        'label' => 'Your E-mail',
        ],
        ];
        $this->add($email);
        //*/

        //对象模式生成
        $element = new Text($inputName);
        $element->setLabel('Your E-mail');
        $element->setAttributes([
            'id' => $inputName,
            'class' => 'login-input form-control',
            'placeholder' => 'email@example.com',
        ]);
        $this->add($element);


        //工厂模式生成过滤和验证
        /**
        $filter = [
            'name' => $inputName,
            'required' => true,
            'break_chain_on_failure' => true,
            'filters' => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'min' => 5,
                        'max' => 45,
                    ],
                ],
                [
                    'name' => 'EmailAddress',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'allow' => Hostname::ALLOW_DNS,
                        'useMxCheck' => false,
                    ],
                ],
            ],
        ];
        $this->getInputFilter()->add($filter);
        //*/


        //对象模式生成过滤和验证.
        //内容过滤器
        $filterStringTrim = new StringTrim();
        $filterStripTags = new StripTags();

        //过滤器链接器
        $filterChain = new FilterChain();
        $filterChain->attach($filterStringTrim);
        $filterChain->attach($filterStripTags);


        //邮箱地址长度验证器
        $validatorStringLen = new StringLength();
        $validatorStringLen->setMin(5);
        $validatorStringLen->setMax(45);
        $validatorStringLen->setMessages([
            StringLength::TOO_SHORT => '请务必确保输入超过 %min% 字符长度.',
            StringLength::TOO_LONG => '请务必确保输入不要超过 %max% 字符长度.',
        ]);

        //邮箱地址语法验证器
        $validatorEmail = new EmailAddress();
        $validatorEmail->setOptions([
            'allow' => Hostname::ALLOW_DNS,
            'useMxCheck' => false,
        ]);
        $validatorEmail->setMessage('请输入一个合法的电子邮箱地址.', EmailAddress::INVALID_FORMAT);

        //验证器连接器
        $validatorChain = new ValidatorChain();
        $validatorChain->attach($validatorStringLen, true, 100); //出错返回, 不执行后续验证器, 权重 100
        $validatorChain->attach($validatorEmail, true); //出错返回, 不执行后续验证器, 权重 1(默认)


        //表单过滤器
        $inputFilter = new Input($inputName);

        $inputFilter->setBreakOnFailure(true); //是否检查下一个表单

        $inputFilter->setRequired(true); //必填表单

        $inputFilter->setFilterChain($filterChain);
        $inputFilter->setValidatorChain($validatorChain);

        $this->getInputFilter()->add($inputFilter);

    }


    private function addPassword()
    {
        $inputName = 'passwd';
        $passwd = [
            'type' => 'password',
            'name' => $inputName,
            'attributes' => [
                'id' => $inputName,
                'class' => 'login-input form-control',
            ],
            'options' => [
                'label' => 'Your Password',
            ],
        ];
        $this->add($passwd);


        $filter = [
            'name' => $inputName,
            'required' => true,
            'break_chain_on_failure' => true,
            'filters' => [
                ['name' => 'StringTrim'],
                ['name' => 'StringToLower'],
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'min' => 2,
                        'max' => 12,
                    ],
                    'messages' => [
                        StringLength::TOO_SHORT => '请确保密码超过 %min% 字符长度.',
                        StringLength::TOO_LONG => '请确保密码不要超过 %max% 字符长度.',
                    ],
                ]
            ],
        ];
        $this->getInputFilter()->add($filter);

    }


    private function addSubmit()
    {
        $submit = [
            'type' => 'button',
            'name' => 'submit',
            'attributes' => [
                'id' => 'submit',
                'type' => 'submit',
                'class' => 'login-btn btn btn-block btn-default',
            ],
            'options' => [
                'label' => 'Submit',
            ],
        ];
        $this->add($submit);
    }


    private function addElements()
    {
        $this->addAccount();
        $this->addPassword();
        $this->addSubmit();
    }

}