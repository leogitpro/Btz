<?php
/**
 * WeChatInvoiceForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Form;


use Form\Form\BaseForm;
use Form\Validator\Factory;


class WeChatInvoiceForm extends BaseForm
{

    /**
     * 表单: 发票抬头
     */
    private function addWeChatInvoiceTitle()
    {
        $this->addTextElement('title', true, [Factory::StringLength(1, 45)]);
    }

    /**
     * 表单: 发票金额
     */
    private function addWeChatInvoiceMoney()
    {
        $this->addTextElement('money', true, [Factory::Regex("/^[1-9][0-9]+$/")]);
    }

    /**
     * 表单: 发票收件人名称
     */
    private function addWeChatInvoiceReceiverName()
    {
        $this->addTextElement('receiver_name', true, [Factory::StringLength(1, 15)]);
    }

    /**
     * 表单: 发票收件人电话
     */
    private function addWeChatInvoiceReceiverPhone()
    {
        $this->addTextElement('receiver_phone', true, [Factory::Regex("/^[0-9\\-]+$/")]);
    }

    /**
     * 表单: 发票收件人地址
     */
    private function addWeChatInvoiceReceiverAddress()
    {
        $this->addTextElement('receiver_address');
    }

    /**
     * 表单: 其他信息
     */
    private function addWeChatInvoiceNote()
    {
        $this->addTextareaElement('note', false);
    }


    public function addElements()
    {
        $this->addWeChatInvoiceTitle();
        $this->addWeChatInvoiceMoney();
        $this->addWeChatInvoiceReceiverName();
        $this->addWeChatInvoiceReceiverPhone();
        $this->addWeChatInvoiceReceiverAddress();
        $this->addWeChatInvoiceNote();
    }

}