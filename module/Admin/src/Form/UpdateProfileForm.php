<?php


namespace Admin\Form;


use Admin\Entity\Member;


class UpdateProfileForm extends BaseForm
{

    /**
     * @var Member
     */
    private $member;


    public function __construct(Member $member)
    {
        $this->member = $member;

        parent::__construct();
    }


    /**
     * 表单: 用户名字
     */
    private function addNameElements()
    {
        $this->addElement([
            'type' => 'text',
            'name' => 'name',
            'attributes' => [
                'id' => 'name',
                'value' => $this->member->getMemberName(),
            ],
            'options' => [
                'label' => 'Full Name',
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
                    'name' => 'StringLength',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'min' => 2,
                        'max' => 15,
                    ],
                ],
            ],
        ]);
    }


    public function addElements()
    {
        $this->addNameElements();
    }

}