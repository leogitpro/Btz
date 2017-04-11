<?php
/**
 * WeChatExpiredForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Form;


use Form\Form\BaseForm;


class WeChatExpiredForm extends BaseForm
{

    /**
     * 表单: 过期时间
     */
    private function addWeChatExpired()
    {
        $this->addDateElement('expired');
    }


    public function addElements()
    {
        $this->addWeChatExpired();
    }

}