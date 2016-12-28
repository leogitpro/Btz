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
                    'name' => 'EmailAddress',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'allow' => \Zend\Validator\Hostname::ALLOW_DNS,
                        'useMxCheck' => false,
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
                    'name'    => 'StringLength',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'min' => 2,
                        'max' => 128
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
                    'name'    => 'StringLength',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'min' => 12,
                        'max' => 4096
                    ],
                ],
            ],
        ]);

    }
}