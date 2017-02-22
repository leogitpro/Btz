<?php
/**
 * Feedback.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Form;


use Zend\Form\Form;
use Zend\InputFilter\InputFilter;


class FeedbackForm extends Form
{
    public function __construct()
    {
        parent::__construct('feedback_form');

        $this->setAttributes(['method' => 'post', 'role' => 'form']);
        $this->setInputFilter(new InputFilter());

        $this->addElements();
        $this->addFilters();
    }


    public function addElements()
    {
        // CSRF field
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

        // Submit field
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


}