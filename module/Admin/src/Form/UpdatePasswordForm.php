<?php


namespace Admin\Form;


use Admin\Service\MemberManager;
use Admin\Validator\OldPasswordValidator;


class UpdatePasswordForm extends BaseForm
{

    /**
     * @var MemberManager
     */
    private $memberManager;


    public function __construct(MemberManager $memberManager)
    {
        $this->memberManager = $memberManager;
        parent::__construct();
    }

    /**
     * 表单: 用户旧密码
     */
    private function addOldPasswordElement()
    {
        $this->addElement([
            'type' => 'password',
            'name' => 'old_password',
            'attributes' => [
                'id' => 'old_password',
            ],
            'options' => [
                'label' => 'Old password',
            ],
        ]);

        $this->addFilter([
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
    }


    /**
     * 表单: 用户新密码
     */
    private function addNewPasswordElement()
    {
        $this->addElement([
            'type' => 'password',
            'name' => 'new_password',
            'attributes' => [
                'id' => 'new_password',
            ],
            'options' => [
                'label' => 'New password',
            ],
        ]);

        $this->addFilter([
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
    }

    /**
     * 表单: 用户确认密码
     */
    private function addConfirmPasswordElement()
    {
        $this->addElement([
            'type' => 'password',
            'name' => 're_new_password',
            'attributes' => [
                'id' => 're_new_password',
            ],
            'options' => [
                'label' => 'Confirm Password',
            ],
        ]);

        $this->addFilter([
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


    public function addElements()
    {
        $this->addOldPasswordElement();
        $this->addNewPasswordElement();
        $this->addConfirmPasswordElement();
    }

}