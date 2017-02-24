<?php
/**
 * WechatForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Form;


use Admin\Entity\Member;
use Admin\Service\WechatManager;
use Admin\Validator\WechatAppIdUniqueValidator;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class WechatForm extends Form
{

    private $wecahtManager;

    private $wechat;

    private $fields;


    public function __construct(WechatManager $wechatManager, $fields = ['*'], $wechat = null)
    {
        parent::__construct('wechat_form');

        $this->setAttributes(['method' => 'post', 'role' => 'form']);

        $this->wecahtManager = $wechatManager;
        $this->fields = (array)$fields;
        $this->wechat = $wechat;

        $this->setInputFilter(new InputFilter());

        $this->addElements();
        $this->addFilters();
    }


    public function addElements()
    {
        $this->add([
            'type'  => 'csrf',
            'name' => 'csrf',
            'attributes' => [],
            'options' => [
                'csrf_options' => [
                    'timeout' => 600
                ]
            ],
        ]);

        if (in_array('*', $this->fields) || in_array('appid', $this->fields)) {
            $this->add([
                'type' => 'text',
                'name' => 'appid',
                'attributes' => [
                    'id' => 'appid',
                    'value' => ($this->wechat instanceof Member) ? $this->wechat->getWxAppId() : '',
                ],
            ]);
        }

        if (in_array('*', $this->fields) || in_array('appsecret', $this->fields)) {
            $this->add([
                'type' => 'text',
                'name' => 'appsecret',
                'attributes' => [
                    'id' => 'appsecret',
                    'value' => ($this->wechat instanceof Member) ? $this->wechat->getWxAppSecret() : '',
                ],
            ]);
        }


        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'id' => 'submit',
                'value' => 'Submit',
            ],
        ]);
    }


    public function addFilters()
    {
        if (in_array('*', $this->fields) || in_array('appid', $this->fields)) {
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
                        'name' => WechatAppIdUniqueValidator::class,
                        'break_chain_on_failure' => true,
                        'options' => [
                            'wechatManager' => $this->wecahtManager,
                            'wechat' => $this->wechat,
                        ],
                    ],
                ],
            ]);
        }


        if (in_array('*', $this->fields) || in_array('appsecret', $this->fields)) {
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

    }

}