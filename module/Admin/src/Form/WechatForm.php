<?php
/**
 * WechatForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Form;


use Admin\Entity\WeChat;
use Admin\Service\WeChatManager;
use Admin\Validator\WechatAppIdUniqueValidator;



class WeChatForm extends BaseForm
{

    /**
     * @var WeChatManager
     */
    private $wm;

    /**
     * @var WeChat
     */
    private $weChat;


    public function __construct(WeChatManager $wm, $weChat = null)
    {
        $this->wm = $wm;
        $this->weChat = $weChat;

        parent::__construct();
    }


    private function addAppIdElement($value = '')
    {
        $this->add([
            'type' => 'text',
            'name' => 'appid',
            'attributes' => [
                'id' => 'appid',
                'value' => $value,
            ],
        ]);

        $this->getInputFilter()->add([
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
                    'name' => 'StringLength',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'min' => 18,
                        'max' => 45,
                        'messages' => [
                            \Zend\Validator\StringLength::TOO_SHORT => '微信的 AppID 不止这么短吧. 你确定是从公众后台号复制过来的?',
                            \Zend\Validator\StringLength::TOO_LONG => '微信的 AppID 有这么长? 你确定是从公众后台号复制过来的?',
                        ],
                    ],
                ],
                [
                    'name' => WeChatAppIdUniqueValidator::class,
                    'break_chain_on_failure' => true,
                    'options' => [
                        'weChatManager' => $this->wm,
                        'weChat' => $this->weChat,
                    ],
                ],
            ],
        ]);
    }


    private function addAppSecretElement($value = '')
    {
        $this->add([
            'type' => 'text',
            'name' => 'appsecret',
            'attributes' => [
                'id' => 'appsecret',
                'value' => $value,
            ],
        ]);

        $this->getInputFilter()->add([
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
        $this->addCsrfElement();

        if(!$this->weChat instanceof WeChat || $this->weChat->getWxChecked() != WeChat::STATUS_CHECKED) {
            $appId = ($this->weChat instanceof Wechat) ? $this->weChat->getWxAppId() : '';
            $this->addAppIdElement($appId);
        }

        $appSecret = ($this->weChat instanceof Wechat) ? $this->weChat->getWxAppSecret() : '';
        $this->addAppSecretElement($appSecret);

        $this->addSubmitElement();

    }

}