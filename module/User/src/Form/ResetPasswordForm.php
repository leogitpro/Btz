<?php
/**
 * Reset password form
 *
 * User: leo
 */

namespace User\Form;


use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class ResetPasswordForm extends Form
{

    /**
     * ResetPasswordForm constructor.
     *
     */
    public function __construct()
    {
        parent::__construct('reset_password_form');

        $this->setAttributes([
            'method' => 'post',
            'role' => 'form',
        ]);

        $this->setInputFilter(new InputFilter());

        $this->addElements();
        $this->addInputFilters();
    }


    /**
     * Add the form elements
     *
     */
    public function addElements()
    {
        // CSRF Safe
        $this->add([
            'type' => 'csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 3600, // 60 minutes
                ],
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


        // Password input
        $this->add([
            'type' => 'password', // Element type
            'name' => 're_password', // Field name
            'attributes' => [ // Array of attributes
                'id' => 're_password',
            ],
            'options' => [ // Array of options
                'label' => 'Confirm password', // Text Label
            ],
        ]);


        // Submit button input
        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'id' => 'submit',
                'value' => 'Reset password',
            ],
        ]);

    }


    /**
     * Add form Filters and Validators
     *
     */
    public function addInputFilters()
    {
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

        $this->getInputFilter()->add([
            'name'     => 're_password',
            'required' => true,
            'break_on_failure' => true,
            'filters'  => [
            ],
            'validators' => [
                [
                    'name'    => 'identical',
                    'options' => [
                        'token' => 'password',
                    ],
                ],
            ],
        ]);

    }

}