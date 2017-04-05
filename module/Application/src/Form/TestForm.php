<?php
/**
 * TestForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Application\Form;


class TestForm extends BaseForm
{

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
            'name'     => 'name',
            'required' => true,
            'break_on_failure' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'StripNewlines'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        //'messages' => [
                            //\Zend\Validator\NotEmpty::IS_EMPTY => '麻烦您简单的给说明一下联络主题!',
                        //],
                    ],
                ],
                [
                    'name'    => 'StringLength',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'min' => 2,
                        'max' => 128,
                        //'messages' => [
                            //\Zend\Validator\StringLength::TOO_SHORT => '主题太简单了吧!',
                            //\Zend\Validator\StringLength::TOO_LONG => '请勿在主题里填写太多内容, 详细的内容请在内容块留言.',
                        //],
                    ],
                ],
            ],
        ]);
    }


    public function addElements()
    {
        $this->addNameElement();
    }

}