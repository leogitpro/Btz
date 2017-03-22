<?php
/**
 * WechatClientForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Form;



class WeChatClientForm extends BaseForm
{


    /**
     * 表单: 客户端名称
     */
    private function addNameElement()
    {
        $this->addElement([
            'type' => 'text',
            'name' => 'name',
            'attributes' => [
                'id' => 'name'
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
                            \Zend\Validator\NotEmpty::IS_EMPTY => '请设置好客户端的名称方便您日后管理!',
                        ],
                    ],
                ],
                [
                    'name' => 'StringLength',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'min' => 4,
                        'max' => 45,
                        'messages' => [
                            \Zend\Validator\StringLength::TOO_SHORT => '名字太短啦, 这样容易和其他的客户端重名哦.',
                            \Zend\Validator\StringLength::TOO_LONG => '名字太长, 感觉电脑屏幕都不够用了都.',
                        ],
                    ],
                ],
            ],
        ]);
    }


    /**
     * 表单: 客户端域名
     */
    private function addDomainElement()
    {
        $this->addElement([
            'type' => 'text',
            'name' => 'domain',
            'attributes' => [
                'id' => 'domain'
            ],
        ]);

        $this->addFilter([
            'name' => 'domain',
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
                            \Zend\Validator\NotEmpty::IS_EMPTY => '请输入允许来访的客户端域名, 这样能更好的包含您的数据安全.',
                        ],
                    ],
                ],

                [
                    'name' => 'StringLength',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'min' => 4,
                        'max' => 255,
                        'messages' => [
                            \Zend\Validator\StringLength::TOO_SHORT => '域名太短啦, 这真的是一个域名么?',
                            \Zend\Validator\StringLength::TOO_LONG => '名字太长, 感觉记不住的说.',
                        ],
                    ],
                ],
            ],
        ]);
    }


    /**
     * 表单: 客户端 IP
     */
    private function addIpElement()
    {
        $this->addElement([
            'type' => 'text',
            'name' => 'ip',
            'attributes' => [
                'id' => 'ip'
            ],
        ]);

        $this->addFilter([
            'name' => 'ip',
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
                            \Zend\Validator\NotEmpty::IS_EMPTY => '允许来访的 IP 地址不能空哦, 否则就被禁止访问了.',
                        ],
                    ],
                ],
            ],
        ]);
    }


    /**
     * 表单: 生效时间
     */
    private function addActiveElement()
    {
        $this->addElement([
            'type' => 'date',
            'name' => 'active',
            'attributes' => [
                'id' => 'active'
            ],
        ]);

        $this->addFilter([
            'name' => 'active',
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
        ]);
    }



    /**
     * 表单: 过期时间
     */
    private function addExpireElement()
    {
        $this->addElement([
            'type' => 'date',
            'name' => 'expire',
            'attributes' => [
                'id' => 'expire'
            ],
        ]);

        $this->addFilter([
            'name' => 'expire',
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
        ]);
    }



    public function addElements()
    {
        $this->addNameElement();
        $this->addDomainElement();
        $this->addIpElement();
        $this->addActiveElement();
        $this->addExpireElement();
    }

}