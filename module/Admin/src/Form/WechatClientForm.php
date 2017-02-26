<?php
/**
 * WechatClientForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Form;


use Admin\Entity\WechatClient;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;


class WechatClientForm extends Form
{

    private $client;

    public function __construct($client = null)
    {
        parent::__construct('wx_client_form');

        $this->setAttributes(['method' => 'post', 'role' => 'form']);

        $this->setInputFilter(new InputFilter());

        $this->client = $client;

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

        $this->add([
            'type' => 'text',
            'name' => 'name',
            'attributes' => [
                'id' => 'name',
                'value' => ($this->client instanceof WechatClient) ? $this->client->getName() : '',
            ],
        ]);

        $this->add([
            'type' => 'text',
            'name' => 'domain',
            'attributes' => [
                'id' => 'domain',
                'value' => ($this->client instanceof WechatClient) ? $this->client->getDomains() : '',
            ],
        ]);

        $this->add([
            'type' => 'text',
            'name' => 'ip',
            'attributes' => [
                'id' => 'ip',
                'value' => ($this->client instanceof WechatClient) ? $this->client->getIps() : '',
            ],
        ]);

        $this->add([
            'type' => 'date',
            'name' => 'active',
            'attributes' => [
                'id' => 'active',
                'value' => ($this->client instanceof WechatClient) ? date('Y-m-d', $this->client->getActiveTime()) : '',
            ],
        ]);

        $this->add([
            'type' => 'date',
            'name' => 'expire',
            'attributes' => [
                'id' => 'expire',
                'value' => ($this->client instanceof WechatClient) ? date('Y-m-d', $this->client->getExpireTime()) : '',
            ],
        ]);

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

        $this->getInputFilter()->add([
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



        $this->getInputFilter()->add([
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

                /**
                [
                    'name' => \Zend\Validator\Hostname::class,
                    'break_chain_on_failure' => true,
                    'options' => [
                        //'allow'       => \Zend\Validator\Hostname::ALLOW_DNS, // Allow these hostnames
                        //'useIdnCheck' => true,  // Check IDN domains
                        //'useTldCheck' => true,  // Check TLD elements
                        //'ipValidator' => null,  // IP validator to use
                        'messages' => [
                            \Zend\Validator\Hostname::INVALID => '这个域名好像不是可用的域名哦.',
                            \Zend\Validator\Hostname::INVALID_DASH => '这个域名好像不是可用的域名哦.',
                            \Zend\Validator\Hostname::INVALID_HOSTNAME => '这个域名好像不是可用的域名哦.',
                            \Zend\Validator\Hostname::INVALID_HOSTNAME_SCHEMA => '这个域名好像不是可用的域名哦.',
                            \Zend\Validator\Hostname::INVALID_LOCAL_NAME => '这个域名好像不是可用的域名哦.',
                            \Zend\Validator\Hostname::INVALID_URI => '这个域名好像不是可用的域名哦.',
                            \Zend\Validator\Hostname::IP_ADDRESS_NOT_ALLOWED => '这个域名好像不是可用的域名哦.',
                            \Zend\Validator\Hostname::LOCAL_NAME_NOT_ALLOWED => '这个域名好像不是可用的域名哦.',
                            \Zend\Validator\Hostname::UNDECIPHERABLE_TLD => '这个域名好像不是可用的域名哦.',
                            \Zend\Validator\Hostname::UNKNOWN_TLD => '这个域名好像不是可用的域名哦.',
                        ],
                    ],
                ],
                //*/
            ],
        ]);



        $this->getInputFilter()->add([
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

}