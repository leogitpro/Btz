<?php
/**
 * WeChatOrderForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Form;


use Form\Form\BaseForm;


class WeChatOrderForm extends BaseForm
{

    private function addWeChatOrderSecond()
    {
        $this->addSelectElement('second', [365 * 24 * 3600 => '1 年']);
    }

    public function addElements()
    {
        $this->addWeChatOrderSecond();
    }

}