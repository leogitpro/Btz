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
use Admin\Service\DepartmentManager;
use Admin\Service\MemberManager;
use Admin\Validator\DeptIdValidator;
use Admin\Validator\MemberIdValidator;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;


class MessageForm extends Form
{
    private $receiver;

    private $manager;

    public function __construct($manager = null, $receiver = null)
    {
        parent::__construct('message_form');

        $this->setAttributes(['method' => 'post', 'role' => 'form']);

        $this->manager = $manager;
        $this->receiver = $receiver;
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


        if ('*' != $this->receiver) {
            $valueName = '';
            $valueId = '';

            if ($this->receiver instanceof Member) {
                $valueName = $this->receiver->getMemberName();
                $valueId = $this->receiver->getMemberId();
            }

            if ($this->receiver instanceof Department) {
                $valueName = $this->receiver->getDeptName();
                $valueId = $this->receiver->getDeptId();
            }

            $this->add([
                'type' => 'text',
                'name' => 'receiver_name',
                'attributes' => [
                    'id' => 'receiver_name',
                    'value' => $valueName,
                ],
                'options' => [],
            ]);

            $this->add([
                'type' => 'hidden',
                'name' => 'receiver_id',
                'attributes' => [
                    'id' => 'receiver_id',
                    'value' => $valueId,
                ]
            ]);
        }

        $this->add([
            'type' => 'text',
            'name' => 'topic',
            'attributes' => [
                'id' => 'topic',
            ],
            'options' => [
                'label' => '消息标题',
            ],
        ]);


        $this->add([
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

        if ('*' != $this->receiver) {

            $this->getInputFilter()->add([
                'name' => 'receiver_name',
                'required' => true,
                'break_on_failure' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 2,
                            'max' => 45
                        ],
                    ],
                ],
            ]);

            if ($this->manager instanceof MemberManager) {
                $this->getInputFilter()->add([
                    'name' => 'receiver_id',
                    'break_on_failure' => true,
                    'filters'  => [],
                    'validators' => [
                        [
                            'name'    => MemberIdValidator::class,
                            'options' => [
                                'memberManager' => $this->manager,
                            ],
                        ],
                    ],
                ]);
            }

            if ($this->manager instanceof DepartmentManager) {
                $this->getInputFilter()->add([
                    'name' => 'receiver_id',
                    'break_on_failure' => true,
                    'filters'  => [],
                    'validators' => [
                        [
                            'name'    => DeptIdValidator::class,
                            'options' => [
                                'deptManager' => $this->manager,
                            ],
                        ],
                    ],
                ]);
            }
        }


        $this->getInputFilter()->add([
            'name' => 'topic',
            'required' => true,
            'break_on_failure' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 2,
                        'max' => 45
                    ],
                ],
            ],
        ]);


        $this->getInputFilter()->add([
            'name' => 'content',
            'required' => true,
            'break_on_failure' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 10,
                        'max' => 4096
                    ],
                ],
            ],
        ]);
    }

}