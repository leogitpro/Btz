<?php

/**
 * Contact us form
 *
 * User: leo
 */

namespace Application\Form;


use Form\Form\BaseForm;
use Form\Validator\Factory;


class ContactUsForm extends BaseForm
{

    /**
     * @var array
     */
    private $captchaConfig;


    /**
     * ContactUsForm constructor.
     *
     * @param array $captchaConfig
     */
    public function __construct($captchaConfig)
    {
        $this->captchaConfig = $captchaConfig;

        parent::__construct();
    }


    /**
     * 表单: 用户邮件
     */
    private function addContactUsEmail()
    {
        $this->addEmailElement('email');
    }


    /**
     * 表单: 主题
     */
    private function addContactUsSubject()
    {
        $validators = [
            Factory::StringLength(2, 128),
        ];

        $this->addTextElement('subject', true, $validators);
    }


    /**
     * 表单: 内容
     */
    private function addContactUsContent()
    {
        $validators = [
            Factory::StringLength(12, 4096),
        ];

        $this->addTextareaElement('message', true, $validators);
    }

    /**
     * 表单: 验证码
     */
    private function addContactUsCaptcha()
    {
        $this->addCaptchaElement('captcha', $this->captchaConfig);
    }


    public function addElements()
    {
        $this->addContactUsEmail();
        $this->addContactUsSubject();
        $this->addContactUsContent();
        $this->addContactUsCaptcha();
    }
}