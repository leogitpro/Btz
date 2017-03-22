<?php
/**
 * WechatQrcodeForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Form;


use Admin\Entity\WeChatQrCode;


class WeChatQrCodeForm extends BaseForm
{

    /**
     * 表单: 二维码名字
     */
    private function addNameElement()
    {
        $this->addElement([
            'type' => 'text',
            'name' => 'name',
            'attributes' => [
                'id' => 'name',
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
                            \Zend\Validator\NotEmpty::IS_EMPTY => '请设置好二维码的名称方便您日后管理!',
                        ],
                    ],
                ],
                [
                    'name' => 'StringLength',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'min' => 2,
                        'max' => 45,
                        'messages' => [
                            \Zend\Validator\StringLength::TOO_SHORT => '名字太短啦, 这样容易和其他的二维码重名哦.',
                            \Zend\Validator\StringLength::TOO_LONG => '名字太长, 感觉电脑屏幕都不够用了都.',
                        ],
                    ],
                ],
            ],
        ]);
    }


    /**
     * 表单: 二维码类型
     */
    private function addTypeElement()
    {
        $this->addElement([
            'type' => 'select',
            'name' => 'type',
            'attributes' => [
                'id' => 'type',
            ],
            'options' => [
                'value_options' => WeChatQrCode::getTypeList(),
            ],
        ]);

        $this->addFilter([
            'name' => 'type',
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
        ]);
    }

    /**
     * 二维码过期时间
     */
    private function addExpiredElement()
    {
        $this->addElement([
            'type' => 'text',
            'name' => 'expired',
            'attributes' => [
                'id' => 'expired',
            ],
        ]);

        $this->addFilter([
            'name' => 'expired',
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
        ]);
    }


    /**
     * 二维码参数
     */
    private function addSceneElement()
    {
        $this->addElement([
            'type' => 'text',
            'name' => 'scene',
            'attributes' => [
                'id' => 'scene',
            ],
        ]);

        $this->addFilter([
            'name' => 'scene',
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
                            \Zend\Validator\NotEmpty::IS_EMPTY => '请设置好二维码的参数, 这个非常重要.',
                        ],
                    ],
                ],
                [
                    'name' => 'StringLength',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'max' => 64,
                        'messages' => [
                            \Zend\Validator\StringLength::TOO_LONG => '参数太长, 微信不允许设置这么长的参数哦.',
                        ],
                    ],
                ],
            ],
        ]);
    }


    public function addElements()
    {
        $this->addNameElement();
        $this->addTypeElement();
        $this->addExpiredElement();
        $this->addSceneElement();
    }

}