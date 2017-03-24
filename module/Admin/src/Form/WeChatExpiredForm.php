<?php
/**
 * WeChatExpiredForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Form;


class WeChatExpiredForm extends BaseForm
{

    /**
     * 表单: 过期时间
     */
    private function addExpiredElement()
    {
        $this->addElement([
            'type' => 'date',
            'name' => 'expired',
            'attributes' => [
                'id' => 'expired',
            ],
            'options' => [
                'label' => 'Expired date',
            ],
        ]);

        $this->addFilter([
            'name' => 'expired',
            'required' => true,
            'break_on_failure' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'messages' => [
                            \Zend\Validator\NotEmpty::IS_EMPTY => '过期时间不能为空!',
                        ],
                    ],
                ],
                [
                    'name'    => 'Date',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'messages' => [
                            \Zend\Validator\Date::INVALID => '过期时间格式设置无效!',
                            \Zend\Validator\Date::INVALID_DATE => '过期时间格式设置无效!',
                        ],
                    ],
                ],
            ],
        ]);
    }


    public function addElements()
    {
        $this->addExpiredElement();
    }

}