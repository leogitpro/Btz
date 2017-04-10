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
use Form\Form\BaseForm;
use Form\Validator\Factory;


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
    private function addMemberAccount()
    {
        $validators = [
            [
                'name' => MemberEmailUniqueValidator::class,
                'break_chain_on_failure' => true,
                'options' => [
                    'memberManager' => $this->memberManager,
                    'member' => $this->member,
                ],
            ]
        ];

        $this->addEmailElement('email', true, $validators);
    }


    /**
     * 表单: 用户密码
     */
    private function addMemberPassword()
    {
        $validators = [
            Factory::StringLength(4, 15),
        ];

        $this->addPasswordElement('password', $validators);
    }


    /**
     * 表单: 用户名字
     */
    private function addMemberName()
    {
        $validators = [
            Factory::StringLength(2, 15),
        ];

        $this->addTextElement('name', true, $validators);
    }


    /**
     * 表单: 账号失效时间
     */
    private function addMemberExpire()
    {
        $this->addDateElement('expired');
    }


    /**
     * 表单: 用户等级
     */
    private function addMemberLevel()
    {
        $options = [
            Member::LEVEL_INTERIOR => Member::getMemberLevelList()[Member::LEVEL_INTERIOR],
            Member::LEVEL_JUNIOR => Member::getMemberLevelList()[Member::LEVEL_JUNIOR],
            Member::LEVEL_SENIOR => Member::getMemberLevelList()[Member::LEVEL_SENIOR],
            Member::LEVEL_SUPERIOR => Member::getMemberLevelList()[Member::LEVEL_SUPERIOR],
        ];

        $this->addSelectElement('level', $options);
    }


    public function addElements()
    {
        if (in_array('*', $this->fields) || in_array('email', $this->fields)) {
            $this->addMemberAccount();
        }

        if (in_array('*', $this->fields) || in_array('password', $this->fields)) {
            $this->addMemberPassword();
        }

        if (in_array('*', $this->fields) || in_array('name', $this->fields)) {
            $this->addMemberName();
        }

        if (in_array('*', $this->fields) || in_array('expired', $this->fields)) {
            $this->addMemberExpire();
        }

        if (in_array('*', $this->fields) || in_array('level', $this->fields)) {
            $this->addMemberLevel();
        }
    }
}