<?php
/**
 * MessageForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Form;


use Admin\Entity\Department;
use Admin\Entity\Member;
use Admin\Validator\DeptIdValidator;
use Admin\Validator\MemberIdValidator;


class MessageForm extends BaseForm
{
    private $receiver;

    private $manager;

    public function __construct($receiver = null, $manager = null)
    {

        $this->manager = $manager;
        $this->receiver = $receiver;

        parent::__construct();
    }


    /**
     * 表单: 消息接收者
     */
    private function addReceiverElement($name, $id, $validator)
    {
        $this->addElement([
            'type' => 'text',
            'name' => 'receiver_name',
            'attributes' => [
                'id' => 'receiver_name',
                'value' => $name,
            ],
            'options' => [],
        ]);
        $this->addElement([
            'type' => 'hidden',
            'name' => 'receiver_id',
            'attributes' => [
                'id' => 'receiver_id',
                'value' => $id,
            ]
        ]);

        $this->addFilter([
            'name' => 'receiver_name',
            'required' => true,
            'break_on_failure' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'messages' => [
                            \Zend\Validator\NotEmpty::IS_EMPTY => '接收者不能为空哦!',
                        ],
                    ],
                ],
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 2,
                        'max' => 45,
                        'messages' => [
                            \Zend\Validator\StringLength::TOO_SHORT => '接收者名字太短了哦!',
                            \Zend\Validator\StringLength::TOO_LONG => '接收者名字太长了哦!',
                        ],
                    ],
                ],
            ],
        ]);

        $this->addFilter([
            'name' => 'receiver_id',
            'break_on_failure' => true,
            'filters'  => [],
            'validators' => [
                $validator,
            ],
        ]);
    }


    /**
     * 表单: 消息标题
     */
    private function addTopicElement()
    {
        $this->addElement([
            'type' => 'text',
            'name' => 'topic',
            'attributes' => [
                'id' => 'topic',
            ],
            'options' => [
                'label' => '消息标题',
            ],
        ]);

        $this->addFilter([
            'name' => 'topic',
            'required' => true,
            'break_on_failure' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'messages' => [
                            \Zend\Validator\NotEmpty::IS_EMPTY => '消息标题不能为空哦!',
                        ],
                    ],
                ],
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 2,
                        'max' => 45,
                        'messages' => [
                            \Zend\Validator\StringLength::TOO_SHORT => '消息标题太短了哦!',
                            \Zend\Validator\StringLength::TOO_LONG => '消息标题太长了哦!',
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * 表单: 消息内容
     */
    private function addConentElement()
    {
        $this->addElement([
            'type' => 'textarea',
            'name' => 'content',
            'attributes' => [
                'id' => 'content',
                'rows' => 5,
                'cols' => 30,
            ],
            'options' => [
                'label' => '消息内容',
            ],
        ]);

        $this->addFilter([
            'name' => 'content',
            'required' => true,
            'break_on_failure' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'messages' => [
                            \Zend\Validator\NotEmpty::IS_EMPTY => '消息内容不能为空哦!',
                        ],
                    ],
                ],
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 10,
                        'max' => 4096,
                        'messages' => [
                            \Zend\Validator\StringLength::TOO_SHORT => '消息内容太短了哦!',
                            \Zend\Validator\StringLength::TOO_LONG => '消息内容太长了哦!',
                        ],
                    ],
                ],
            ],
        ]);
    }


    public function addElements()
    {
        if ('*' != $this->receiver) {
            $valueName = '';
            $valueId = '';
            $validator = null;

            if ($this->receiver instanceof Member) {
                $valueName = $this->receiver->getMemberName();
                $valueId = $this->receiver->getMemberId();
                $validator = [
                    'name'    => MemberIdValidator::class,
                    'options' => [
                        'memberManager' => $this->manager,
                    ],
                ];
            }

            if ($this->receiver instanceof Department) {
                $valueName = $this->receiver->getDeptName();
                $valueId = $this->receiver->getDeptId();
                $validator = [
                    'name'    => DeptIdValidator::class,
                    'options' => [
                        'deptManager' => $this->manager,
                    ],
                ];
            }

            $this->addReceiverElement($valueName, $valueId, $validator);
        }

        $this->addTopicElement();
        $this->addConentElement();

    }

}