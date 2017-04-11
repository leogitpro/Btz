<?php
/**
 * WechatClientForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Form;


use Form\Form\BaseForm;
use Form\Validator\Factory;


class WeChatClientForm extends BaseForm
{

    /**
     * 表单: 客户端名称
     */
    private function addWeChatClientName()
    {
        $this->addTextElement('name', true, [Factory::StringLength(4, 45)]);
    }


    /**
     * 表单: 客户端域名
     */
    private function addWeChatClientDomain()
    {
        $this->addTextElement('domain', true, [Factory::Hostname()]);
    }

    /**
     * 表单: 客户端 IP
     */
    private function addWeChatClientIp()
    {
        $this->addTextElement('ip', true, [Factory::Ip()]);
    }

    /**
     * 表单: 生效时间
     */
    private function addWeChatClientActive()
    {
        $this->addDateElement('active');
    }

    /**
     * 表单: 过期时间
     */
    private function addWeChatClientExpire()
    {
        $this->addDateElement('expire');
    }


    public function addElements()
    {
        $this->addWeChatClientName();
        $this->addWeChatClientDomain();
        $this->addWeChatClientIp();
        $this->addWeChatClientActive();
        $this->addWeChatClientExpire();
    }

}