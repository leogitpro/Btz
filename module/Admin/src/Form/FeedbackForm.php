<?php
/**
 * FeedbackForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Form;


class FeedbackForm extends BaseForm
{

    /**
     * 表单: 反馈内容
     */
    private function addContentElement()
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
                'label' => '',
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
                            \Zend\Validator\NotEmpty::IS_EMPTY => '反馈内容不能为空哦!',
                        ],
                    ],
                ],
                [
                    'name'    => 'StringLength',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'min' => 10,
                        'max' => 4096,
                        'messages' => [
                            \Zend\Validator\StringLength::TOO_SHORT => '反馈的内容是不是太少了点? 请再说的详细一点吧, 谢谢!',
                            \Zend\Validator\StringLength::TOO_LONG => '我想说是不是太多了点? 这是带给了您多大的仇恨哪. 内容太多了啦!',
                        ],
                    ],
                ],
            ],
        ]);
    }


    public function addElements()
    {
        $this->addContentElement();
    }

}