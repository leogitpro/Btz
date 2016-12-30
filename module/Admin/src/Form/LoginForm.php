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
                'label' => 'Account(E-mail)',
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
                'label' => 'Password',
            ],
        ]);

        // Submit field
        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'id' => 'submit',
                'value' => 'Sign In',
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
                    'name' => 'EmailAddress',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'allow' => \Zend\Validator\Hostname::ALLOW_DNS,
                        'useMxCheck' => false,
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
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 4,
                        'max' => 15
                    ],
                ],
            ],
        ]);
    }

}