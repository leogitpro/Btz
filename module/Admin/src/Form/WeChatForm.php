<?php
/**
 * WechatForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Form;


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
     *
     * @param string $value
     */
    private function addAppIdElement($value = '')
    {
        $this->addElement([
            'type' => 'text',
            'name' => 'appid',
            'attributes' => [
                'id' => 'appid',
                'value' => $value,
            ],
        ]);

        $this->addFilter([
            'name' => 'appid',
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
                            \Zend\Validator\NotEmpty::IS_EMPTY => '微信的 AppID 不填写后面没法继续愉快的玩耍了!',
                        ],
                    ],
                ],
                [
                    'name'    => 'Regex',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'pattern' => "/^wx[0-9a-z]+$/",
                        'messages' => [
                            \Zend\Validator\Regex::NOT_MATCH=> '请填写正确的 AppID, 请注意大小写!',
                        ],
                    ],
                ],
                [
                    'name' => AppIdUniqueValidator::class,
                    'break_chain_on_failure' => true,
                    'options' => [
                        'accountService' => $this->wm,
                        'account' => $this->weChat,
                    ],
                ],
            ],
        ]);
    }


    /**
     * 表单: AppSecret
     *
     * @param string $value
     */
    private function addAppSecretElement($value = '')
    {
        $this->addElement([
            'type' => 'text',
            'name' => 'appsecret',
            'attributes' => [
                'id' => 'appsecret',
                'value' => $value,
            ],
        ]);

        $this->addFilter([
            'name' => 'appsecret',
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
                            \Zend\Validator\NotEmpty::IS_EMPTY => '微信的 AppSecret 不填写后面没法继续愉快的玩耍了哦!',
                        ],
                    ],
                ],
                [
                    'name'    => 'Regex',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'pattern' => "/^[0-9a-z]+$/",
                        'messages' => [
                            \Zend\Validator\Regex::NOT_MATCH=> '请填写正确的 AppSecret, 请注意大小写!',
                        ],
                    ],
                ],
                [
                    'name' => 'StringLength',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'min' => 18,
                        'max' => 255,
                        'messages' => [
                            \Zend\Validator\StringLength::TOO_SHORT => '微信的 AppSecret 不止这么短吧. 你确定是从公众后台号复制过来的?',
                            \Zend\Validator\StringLength::TOO_LONG => '微信的 AppSecret 有这么长? 你确定是从公众后台号复制过来的?',
                        ],
                    ],
                ],
            ],
        ]);
    }


    public function addElements()
    {
        if(!$this->weChat instanceof Account || $this->weChat->getWxChecked() != Account::STATUS_CHECKED) {
            $appId = ($this->weChat instanceof Account) ? $this->weChat->getWxAppId() : '';
            $this->addAppIdElement($appId);
        }

        $appSecret = ($this->weChat instanceof Account) ? $this->weChat->getWxAppSecret() : '';
        $this->addAppSecretElement($appSecret);
    }

}