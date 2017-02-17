<?php
/**
 * MemberForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Form;


use Admin\Entity\Member;
use Admin\Service\MemberManager;
use Admin\Validator\MemberEmailUniqueValidator;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class MemberForm extends Form
{

    /**
     * @var MemberManager
     */
    private $memberManager;

    /**
     * @var Member
     */
    private $member;

    /**
     * @var array
     */
    private $fields;


    public function __construct(MemberManager $memberManager, $member = null, $fields = ['*'])
    {
        parent::__construct('member_form');

        $this->memberManager = $memberManager;
        $this->member = $member;
        $this->fields = $fields;

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

        if (in_array('*', $this->fields) || in_array('email', $this->fields)) {
            $this->add([
                'type' => 'text',
                'name' => 'email',
                'attributes' => [
                    'id' => 'email',
                    'value' => (null == $this->member) ? '' : $this->member->getMemberEmail(),
                ],
                'options' => [
                    'label' => 'Member account(E-mail)',
                ],
            ]);
        }



        if (in_array('*', $this->fields) || in_array('password', $this->fields)) {
            $this->add([
                'type' => 'password',
                'name' => 'password',
                'attributes' => [
                    'id' => 'password',
                ],
                'options' => [
                    'label' => 'Member password',
                ],
            ]);
        }


        if (in_array('*', $this->fields) || in_array('name', $this->fields)) {
            $this->add([
                'type' => 'text',
                'name' => 'name',
                'attributes' => [
                    'id' => 'name',
                    'value' => (null == $this->member) ? '' : $this->member->getMemberName(),
                ],
                'options' => [
                    'label' => 'Member name',
                ],
            ]);
        }


        if (in_array('*', $this->fields) || in_array('status', $this->fields)) {
            $this->add([
                'type' => 'select',
                'name' => 'status',
                'attributes' => [
                    'id' => 'status',
                    'value' => (null == $this->member) ? Member::STATUS_RETRIED : $this->member->getMemberStatus(),
                ],
                'options' => [
                    'label' => 'Member status',
                    'value_options' => [
                        Member::STATUS_ACTIVATED => Member::getMemberStatusList()[Member::STATUS_ACTIVATED],
                        Member::STATUS_RETRIED => Member::getMemberStatusList()[Member::STATUS_RETRIED],
                    ],
                ],
            ]);
        }


        if (in_array('*', $this->fields) || in_array('level', $this->fields)) {
            $this->add([
                'type' => 'select',
                'name' => 'level',
                'attributes' => [
                    'id' => 'level',
                    'value' => (null == $this->member) ? Member::LEVEL_INTERIOR: $this->member->getMemberLevel(),
                ],
                'options' => [
                    'label' => 'Member level',
                    'value_options' => [
                        Member::LEVEL_INTERIOR => Member::getMemberLevelList()[Member::LEVEL_INTERIOR],
                        Member::LEVEL_JUNIOR => Member::getMemberLevelList()[Member::LEVEL_JUNIOR],
                        Member::LEVEL_SENIOR => Member::getMemberLevelList()[Member::LEVEL_SENIOR],
                        Member::LEVEL_SUPERIOR => Member::getMemberLevelList()[Member::LEVEL_SUPERIOR],
                    ],
                ],
            ]);
        }


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
        if (in_array('*', $this->fields) || in_array('email', $this->fields)) {

            $this->getInputFilter()->add([
                'name' => 'email',
                'required' => true,
                'break_on_failure' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'EmailAddress',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'allow' => \Zend\Validator\Hostname::ALLOW_DNS,
                            'useMxCheck' => false,
                        ],
                    ],
                    [
                        'name' => MemberEmailUniqueValidator::class,
                        'break_chain_on_failure' => true,
                        'options' => [
                            'memberManager' => $this->memberManager,
                            'member' => $this->member,
                        ],
                    ],
                ],
            ]);
        }

        if (in_array('*', $this->fields) || in_array('password', $this->fields)) {

            $this->getInputFilter()->add([
                'name'     => 'password',
                'required' => true,
                'break_on_failure' => true,
                'filters'  => [
                    [
                        'name' => 'StringToLower',
                        'options' => [
                            'encoding' => 'UTF-8',
                        ],
                    ],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 4,
                            'max' => 15
                        ],
                    ],
                ],
            ]);
        }

        if (in_array('*', $this->fields) || in_array('name', $this->fields)) {

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
                        'name' => 'StringLength',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'min' => 2,
                            'max' => 45,
                        ],
                    ],
                ],
            ]);
        }
    }

}