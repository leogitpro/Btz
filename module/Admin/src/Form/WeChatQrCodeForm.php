<?php
/**
 * WechatQrcodeForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Form;


use Form\Form\BaseForm;
use Form\Validator\Factory;
use WeChat\Entity\QrCode;


class WeChatQrCodeForm extends BaseForm
{

    /**
     * 表单: 二维码名字
     */
    private function addWeChatQrCodeName()
    {
        $this->addTextElement('name', true, [Factory::StringLength(2, 45)]);
    }

    /**
     * 表单: 二维码类型
     */
    private function addWeChatQrCodeType()
    {
        $this->addSelectElement('type', QrCode::getTypeList());
    }

    /**
     * 二维码过期时间
     */
    private function addWeChatQrCodeExpired()
    {
        $this->addTextElement('expired', true, [Factory::Regex("/^[1-9][0-9]{1,5}$/")]);
    }

    /**
     * 二维码参数
     */
    private function addWeChatQrCodeScene()
    {
        $this->addTextElement('scene', true, [Factory::Regex("/^[a-zA-Z0-9]{1,64}$/")]);
    }


    public function addElements()
    {
        $this->addWeChatQrCodeName();
        $this->addWeChatQrCodeType();
        $this->addWeChatQrCodeExpired();
        $this->addWeChatQrCodeScene();
    }

}