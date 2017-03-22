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


class MemberForm extends BaseForm
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

        $this->memberManager = $memberManager;
        $this->member = $member;
        $this->fields = $fields;

        parent::__construct();
    }


    /**
     * 表单: 用户帐号
     */
    private function addAccountElement()
    {
        $this->addElement([
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

        $this->addFilter([
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


    /**
     * 表单: 用户密码
     */
    private function addPasswordElement()
    {
        $this->addElement([
            'type' => 'password',
            'name' => 'password',
            'attributes' => [
                'id' => 'password',
            ],
            'options' => [
                'label' => 'Member password',
            ],
        ]);

        $this->addFilter([
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


    /**
     * 表单: 用户名字
     */
    private function addNameElement()
    {
        $this->addElement([
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
                        'max' => 45,
                    ],
                ],
            ],
        ]);
    }


    /**
     * 表单: 账号失效时间
     */
    private function addExpireElement()
    {
        $this->addElement([
            'type' => 'date',
            'name' => 'expired',
            'attributes' => [
                'id' => 'expired',
                'value' => (null == $this->member) ? '' : $this->member->getMemberExpired()->format('Y-m-d'),
            ],
            'options' => [
                'label' => 'Member expired',
            ],
        ]);

        //$this->addFilter();
    }


    /**
     * 表单: 用户等级
     */
    private function addLevelElement()
    {
        $this->addElement([
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

        //$this->addFilter();
    }


    public function addElements()
    {

        if (in_array('*', $this->fields) || in_array('email', $this->fields)) {
            $this->addAccountElement();
        }

        if (in_array('*', $this->fields) || in_array('password', $this->fields)) {
            $this->addPasswordElement();
        }

        if (in_array('*', $this->fields) || in_array('name', $this->fields)) {
            $this->addNameElement();
        }

        if (in_array('*', $this->fields) || in_array('expired', $this->fields)) {
            $this->addExpireElement();
        }

        if (in_array('*', $this->fields) || in_array('level', $this->fields)) {
            $this->addLevelElement();
        }
    }
}