<?php
/**
 * UpdatePasswordForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Form;


use Admin\Service\MemberManager;
use Admin\Validator\OldPasswordValidator;
use Form\Form\BaseForm;
use Form\Validator\Factory;


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
    private function addOldPassword()
    {
        $validators = [
            Factory::StringLength(4, 15),
            [
                'name'    => OldPasswordValidator::class,
                'break_chain_on_failure' => true,
                'options' => [
                    'memberManager' => $this->memberManager,
                ],
            ]
        ];

        $this->addPasswordElement('old_password', $validators);
    }


    /**
     * 表单: 用户新密码
     */
    private function addNewPassword()
    {
        $validators = [
            Factory::StringLength(4, 15),
        ];

        $this->addPasswordElement('new_password', $validators);
    }

    /**
     * 表单: 用户确认密码
     */
    private function addConfirmPassword()
    {
        $validators = [
            Factory::Identical('new_password'),
        ];

        $this->addPasswordElement('re_new_password', $validators);
    }


    public function addElements()
    {
        $this->addOldPassword();
        $this->addNewPassword();
        $this->addConfirmPassword();
    }

}