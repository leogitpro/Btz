<?php
/**
 * ApplyForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Application\Form;



use Admin\Service\MemberManager;
use Admin\Validator\MemberEmailUniqueValidator;
use Form\Form\BaseForm;
use Form\Validator\Factory;
use WeChat\Service\AccountService;
use WeChat\Validator\AppIdUniqueValidator;


class ApplyForm extends BaseForm
{

    /**
     * @var array
     */
    private $captchaConfig;

    /**
     * @var MemberManager
     */
    private $memberManager;

    /**
     * @var AccountService
     */
    private $accountService;


    public function __construct(MemberManager $memberManager, AccountService $accountService, $captcha_config)
    {
        $this->captchaConfig = $captcha_config;
        $this->memberManager = $memberManager;
        $this->accountService = $accountService;

        parent::__construct();
    }


    /**
     * 表单: 用户账号
     */
    private function addApplyEmailElement()
    {
        $validators = [
            [
                'name' => MemberEmailUniqueValidator::class,
                'break_chain_on_failure' => true,
                'options' => [
                    'memberManager' => $this->memberManager,
                ],
            ],
        ];

        $this->addEmailElement('email', true, $validators);
    }


    /**
     * 表单: 用户名称
     */
    private function AddApplyName()
    {
        $validators = [
            Factory::StringLength(2, 15),
        ];

        $this->addTextElement('name', true, $validators);
    }


    /**
     * 表单: 微信 AppID
     */
    private function addApplyWeChatAppId()
    {
        $validators = [
            Factory::Regex("/^wx[0-9a-z]+$/"),
            [
                'name' => AppIdUniqueValidator::class,
                'break_chain_on_failure' => true,
                'options' => [
                    'accountService' => $this->accountService,
                ],
            ],
        ];
        $this->addTextElement('appid', true, $validators);
    }


    /**
     * 表单: 微信 AppSecret
     */
    private function addApplyWeChatAppSecret()
    {
        $validators = [
            Factory::Regex("/^[0-9a-zA-Z]+$/"),
        ];
        $this->addTextElement('appsecret', true, $validators);
    }


    /**
     * 表单: 验证码
     */
    public function addApplyCaptcha()
    {
        $this->addCaptchaElement('captcha', $this->captchaConfig);
    }


    public function addElements()
    {
        $this->addApplyEmailElement();
        $this->addApplyName();
        $this->addApplyWeChatAppId();
        $this->addApplyWeChatAppSecret();
        $this->addApplyCaptcha();
    }

}