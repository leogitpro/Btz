<?php
/**
 * WechatForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Form;


use Form\Form\BaseForm;
use Form\Validator\Factory;
use WeChat\Entity\Account;
use WeChat\Service\AccountService;
use WeChat\Validator\AppIdUniqueValidator;


class WeChatForm extends BaseForm
{

    /**
     * @var AccountService
     */
    private $wm;

    /**
     * @var Account
     */
    private $weChat;


    public function __construct(AccountService $wm, $weChat = null)
    {
        $this->wm = $wm;
        $this->weChat = $weChat;

        parent::__construct();
    }

    /**
     * 表单: AppID
     */
    private function addWeChatAppId()
    {
        $validators = [
            Factory::Regex("/^wx[0-9a-z]+$/"),
            [
                'name' => AppIdUniqueValidator::class,
                'break_chain_on_failure' => true,
                'options' => [
                    'accountService' => $this->wm,
                    'account' => $this->weChat,
                ],
            ],
        ];

        $this->addTextElement('appid', true, $validators);
    }

    /**
     * 表单: AppSecret
     */
    private function addWeChatAppSecret()
    {
        $validators = [
            Factory::Regex("/^[0-9a-z]{18, 255}$/"),
        ];
        $this->addTextElement('appsecret', true, $validators);
    }


    public function addElements()
    {
        if(!$this->weChat instanceof Account || $this->weChat->getWxChecked() != Account::STATUS_CHECKED) {
            $this->addWeChatAppId();
        }
        $this->addWeChatAppSecret();
    }

}