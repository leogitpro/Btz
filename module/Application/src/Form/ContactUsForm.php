<?php

/**
 * Contact us form
 *
 * User: leo
 */

namespace Application\Form;


use Zend\Form\Form;
use Zend\InputFilter\InputFilter;


class ContactUsForm extends Form
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
        parent::__construct('contact_us_form');

        $this->captchaConfig = $captchaConfig;

        $this->setAttributes(['method' => 'post', 'role' => 'form']);

        $this->setInputFilter(new InputFilter());

        $this->addElements();
        $this->addInputFilters();
    }


    /**
     * Add the form fields
     *
     */
    public function addElements()
    {
        // Add the CSRF field
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

        // Add the E-mail field
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

        // Add the Subject field
        $this->add([
            'type' => 'text',
            'name' => 'subject',
            'attributes' => [
                'id' => 'subject',
            ],
            'options' => [
                'label' => 'Subject',
            ],
        ]);

        // Add the message field
        $this->add([
            'type'  => 'textarea',
            'name' => 'message',
            'attributes' => [
                'id' => 'message'
            ],
            'options' => [
                'label' => 'Message content',
            ],
        ]);

        // Add captcha field
        $this->add([
            'type' => 'captcha',
            'name' => 'captcha',
            'options' => [
                'label' => 'Verification code',
                'captcha' => $this->captchaConfig,
            ],
        ]);


        // Add submit field
        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Submit',
            ],
        ]);
    }


    public function addInputFilters()
    {
        // Add E-mail filter and validators
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
                        ],
                    ],
                ],
            ],
        ]);

        // Add Subject filter and validators
        $this->getInputFilter()->add([
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

        // Add Message content filter and validators
        $this->getInputFilter()->add([
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
}