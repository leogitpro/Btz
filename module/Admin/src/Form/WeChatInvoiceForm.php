<?php
/**
 * WeChatInvoiceForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Form;


class WeChatInvoiceForm extends BaseForm
{

    /**
     * 表单: 发票抬头
     */
    private function addTitleElement()
    {
        $this->addElement([
            'type' => 'text',
            'name' => 'title',
            'attributes' => [
                'id' => 'title',
            ],
        ]);

        $this->addFilter([
            'name' => 'name',
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
                            \Zend\Validator\NotEmpty::IS_EMPTY => '请填写您要开具的发票抬头. 不能留空的.',
                        ],
                    ],
                ],
                [
                    'name' => 'StringLength',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'min' => 1,
                        'max' => 45,
                        'messages' => [
                            \Zend\Validator\StringLength::TOO_SHORT => '抬头太短了.',
                            \Zend\Validator\StringLength::TOO_LONG => '抬头太长了.',
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * 表单: 发票金额
     */
    private function addMoneyElement()
    {
        $this->addElement([
            'type' => 'text',
            'name' => 'money',
            'attributes' => [
                'id' => 'money',
            ],
        ]);

        $this->addFilter();
    }

    /**
     * 表单: 发票收件人名称
     */
    private function addReceiverElement()
    {
        $this->addElement();
        $this->addFilter();
    }

    /**
     * 表单: 发票收件人电话
     */
    private function addPhoneElement()
    {
        $this->addElement();
        $this->addFilter();
    }

    /**
     * 表单: 发票收件人地址
     */
    private function addAddressElement()
    {
        $this->addElement();
        $this->addFilter();
    }

    /**
     * 表单: 其他信息
     */
    private function addNoteElement()
    {
        $this->addElement();
        $this->addFilter();
    }

}