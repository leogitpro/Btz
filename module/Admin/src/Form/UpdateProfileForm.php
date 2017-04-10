<?php
/**
 * UpdateProfileForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Form;


use Form\Form\BaseForm;
use Form\Validator\Factory;


class UpdateProfileForm extends BaseForm
{

    /**
     * 表单: 用户名字
     */
    private function addUpdateProfileName()
    {
        $validators = [
            Factory::StringLength(2, 15),
        ];

        $this->addTextElement('name', true, $validators);
    }


    public function addElements()
    {
        $this->addUpdateProfileName();
    }

}