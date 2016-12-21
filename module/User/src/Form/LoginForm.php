<?php
/**
 * User login form
 *
 * User: leo
 */

namespace User\Form;


use Zend\Form\Form;
use Zend\InputFilter\InputFilter;


class LoginForm extends Form
{

    public function __construct()
    {
        parent::__construct('login_form');

        $this->setAttributes([
            'method' => 'post',
            'role' => 'form',
        ]);

        $this->setInputFilter(new InputFilter());

        $this->addElements();
        $this->addInputFilters();
    }

    public function addElements()
    {
        // CSRF Safe
        $this->add([
            'type' => 'csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 3600, // 10 minutes
                ],
            ],
        ]);


        // Text email input
        $this->add([
            'type' => 'text', // Element type
            'name' => 'email', // Field name
            'attributes' => [ // Array of attributes
                'id' => 'email',
            ],
            'options' => [ // Array of options
                'label' => 'Your E-mail', // Text Label
            ],
        ]);


        // Password input
        $this->add([
            'type' => 'password', // Element type
            'name' => 'password', // Field name
            'attributes' => [ // Array of attributes
                'id' => 'password',
            ],
            'options' => [ // Array of options
                'label' => 'Password', // Text Label
            ],
        ]);


        // Submit button input
        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'id' => 'submit',
                'value' => 'Sign in',
            ],
        ]);
    }



    public function addInputFilters()
    {
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
                    'options' => [
                        'allow' => \Zend\Validator\Hostname::ALLOW_DNS,
                        'useMxCheck' => false,
                    ],
                ],
            ],
        ]);

        $this->getInputFilter()->add([
            'name'     => 'password',
            'required' => true,
            'break_on_failure' => true,
            'filters'  => [
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