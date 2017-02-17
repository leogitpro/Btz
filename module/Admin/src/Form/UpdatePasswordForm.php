<?php


namespace Admin\Form;


use Admin\Service\MemberManager;
use Admin\Validator\OldPasswordValidator;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;


class UpdatePasswordForm extends Form
{

    /**
     * @var MemberManager
     */
    private $memberManager;


    public function __construct(MemberManager $memberManager)
    {

        parent::__construct('update_password_form');

        $this->memberManager = $memberManager;

        $this->setAttributes(['method' => 'post', 'role' => 'form']);

        $this->setInputFilter(new InputFilter());

        $this->addElements();
        $this->addFilters();

    }

    public function addElements()
    {
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

        $this->add([
            'type' => 'password',
            'name' => 'old_password',
            'attributes' => [
                'id' => 'old_password',
            ],
            'options' => [
                'label' => 'Old password',
            ],
        ]);

        $this->add([
            'type' => 'password',
            'name' => 'new_password',
            'attributes' => [
                'id' => 'new_password',
            ],
            'options' => [
                'label' => 'New password',
            ],
        ]);

        $this->add([
            'type' => 'password',
            'name' => 're_new_password',
            'attributes' => [
                'id' => 're_new_password',
            ],
            'options' => [
                'label' => 'Confirm Password',
            ],
        ]);

        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'id' => 'submit',
                'value' => 'Update password',
            ],
        ]);
    }

    public function addFilters()
    {
        $this->getInputFilter()->add([
            'name'     => 'old_password',
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
                    'break_chain_on_failure' => true,
                    'options' => [
                        'min' => 4,
                        'max' => 15
                    ],
                ],
                [
                    'name'    => OldPasswordValidator::class,
                    'break_chain_on_failure' => true,
                    'options' => [
                        'memberManager' => $this->memberManager,
                    ],
                ],
            ],
        ]);

        $this->getInputFilter()->add([
            'name'     => 'new_password',
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

        $this->getInputFilter()->add([
            'name'     => 're_new_password',
            'required' => true,
            'break_on_failure' => true,
            'filters'  => [
            ],
            'validators' => [
                [
                    'name'    => 'identical',
                    'options' => [
                        'token' => 'new_password',
                    ],
                ],
            ],
        ]);
    }

}