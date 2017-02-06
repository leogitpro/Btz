<?php
/**
 * MessageForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Form;


use Zend\Form\Form;
use Zend\InputFilter\InputFilter;


class MessageForm extends Form
{


    public function __construct()
    {
        parent::__construct('message_form');

        $this->setAttributes(['method' => 'post', 'role' => 'form']);

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


        $this->add([
            'type' => 'text',
            'name' => 'topic',
            'attributes' => [
                'id' => 'topic',
            ],
            'options' => [
                'label' => 'Topic',
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
                'label' => 'Content',
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