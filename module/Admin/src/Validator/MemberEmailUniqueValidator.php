<?php
/**
 * MemberEmailUniqueValidator.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Validator;


use Zend\Validator\AbstractValidator;


class MemberEmailUniqueValidator extends AbstractValidator
{

    const EMAIL_EXISTED = 'memberEmailExisted';

    protected $options = [
        'memberManager' => null,
        'member' => null,
    ];

    protected $messageTemplates = [
        self::EMAIL_EXISTED => '该电子邮件地址已经被使用',
    ];


    public function __construct($options = null)
    {
        if (is_array($options)) {
            if (isset($options['memberManager'])) {
                $this->options['memberManager'] = $options['memberManager'];
            }
            if (isset($options['member'])) {
                $this->options['member'] = $options['member'];
            }
        }

        parent::__construct($options);
    }


    /**
     * Check the mail address is unique.
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {
        $memberManager = $this->options['memberManager'];
        $member = $this->options['member'];

        $existedMember = $memberManager->getMemberByEmail($value);

        if (null == $member) {
            if (null == $existedMember) {
                return true;
            } else {
                $this->error(self::EMAIL_EXISTED);
                return false;
            }
        } else {
            $email = $member->getMemberEmail();
            if ($email == $value) {
                return true;
            } else {
                if (null == $existedMember) {
                    return true;
                } else {
                    $this->error(self::EMAIL_EXISTED);
                    return false;
                }
            }
        }
    }


}