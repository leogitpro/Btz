<?php
/**
 * Update user password by old password form
 *
 * User: leo
 */

namespace User\Form;


use User\Service\UserManager;
use User\Validator\OldPasswordValidator;
use Zend\Authentication\AuthenticationService;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class UpdatePasswordForm extends Form
{

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var AuthenticationService
     */
    private $authService;


    public function __construct(UserManager $userManager, AuthenticationService $authService)
    {
        parent::__construct('update_password_form');

        $this->userManager = $userManager;
        $this->authService = $authService;

        $this->setAttributes(['method' => 'post', 'role' => 'form']);

        $this->setInputFilter(new InputFilter());

        $this->addElements();
        $this->addInputFilters();
    }


    public function addElements()
    {
        $this->add([ // CSRF Safe
            'type' => 'csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600, // 10 minutes
                ],
            ],
        ]);

        $this->add([ // Old password input
            'type' => 'password',
            'name' => 'old_password',
            'attributes' => [
                'id' => 'old_password',
            ],
            'options' => [
                'label' => 'Old password',
            ],
        ]);

        $this->add([ // New password input
            'type' => 'password',
            'name' => 'new_password',
            'attributes' => [
                'id' => 'new_password',
            ],
            'options' => [
                'label' => 'New password',
            ],
        ]);

        $this->add([ // New confirm password input
            'type' => 'password',
            'name' => 're_new_password',
            'attributes' => [
                'id' => 're_new_password',
            ],
            'options' => [
                'label' => 'Confirm password',
            ],
        ]);

        $this->add([ // Submit
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'id' => 'submit',
                'value' => 'Update password',
            ],
        ]);
    }


    public function addInputFilters()
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
                        'userManager' => $this->userManager,
                        'authService' => $this->authService,
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