<?php
/**
 * WeChatOrderForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Form;


class WeChatOrderForm extends BaseForm
{

    private function addSecondElement()
    {
        $this->addElement([
            'type' => 'select',
            'name' => 'second',
            'attributes' => [
                'id' => 'second',
            ],
            'options' => [
                'label' => 'Member level',
                'value_options' => [
                    365 * 24 * 3600 => '1 年',
                ],
            ],
        ]);

        //$this->addFilter();
    }


    public function addElements()
    {
        $this->addSecondElement();
    }

}