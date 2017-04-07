<?php
/**
 * Member login form generator
 */

namespace Admin\Form;


use Form\Form\BaseForm;
use Form\Validator\Factory;


class LoginForm extends BaseForm
{

    /**
     * 表单: 登录帐号
     */
    private function addLoginAccount()
    {
        $this->addEmailElement('email');
    }

    /**
     * 表单: 登录密码
     */
    private function addLoginPassword()
    {
        $validators = [
            Factory::StringLength(4, 20),
        ];

        $this->addPasswordElement('password', $validators);
    }


    public function addElements()
    {
        $this->addLoginAccount();
        $this->addLoginPassword();
    }
}