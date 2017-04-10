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
use Form\Form\BaseForm;
use Form\Validator\Factory;


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
    private function addMessageReceiver($validator)
    {
        $validators = [
            Factory::StringLength(2, 45),
        ];
        $this->addTextElement('receiver_name', true, $validators);

        $this->addHiddenElement('receiver_id', [$validator]);
    }

    /**
     * 表单: 消息标题
     */
    private function addMessageTopic()
    {
        $validators = [
            Factory::StringLength(2, 45),
        ];
        $this->addTextElement('topic', true, $validators);
    }


    /**
     * 表单: 消息内容
     */
    private function addMessageContent()
    {
        $validators = [
            Factory::StringLength(10, 4096),
        ];

        $this->addTextareaElement('content', true, $validators);
    }


    public function addElements()
    {
        if ('*' != $this->receiver) {
            $validator = null;

            if ($this->receiver instanceof Member) {
                $validator = [
                    'name'    => MemberIdValidator::class,
                    'options' => [
                        'memberManager' => $this->manager,
                    ],
                ];
            }

            if ($this->receiver instanceof Department) {
                $validator = [
                    'name'    => DeptIdValidator::class,
                    'options' => [
                        'deptManager' => $this->manager,
                    ],
                ];
            }

            $this->addMessageReceiver($validator);
        }

        $this->addMessageTopic();
        $this->addMessageContent();
    }

}