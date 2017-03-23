<?php
/**
 * MemberIdValidator.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Validator;


use Admin\Entity\Member;
use Admin\Exception\InvalidArgumentException;
use Admin\Service\MemberManager;
use Zend\Validator\AbstractValidator;


class MemberIdValidator extends AbstractValidator
{

    const ID_INVALID = 'memberIdInvalid';

    protected $options = [
        'memberManager' => null
    ];

    protected $messageTemplates = [
        self::ID_INVALID => '成员信息无效',
    ];


    public function __construct($options = null)
    {
        if (is_array($options)) {
            if (isset($options['memberManager'])) {
                $this->options['memberManager'] = $options['memberManager'];
            }
        }

        parent::__construct($options);
    }


    /**
     * Check the member id is existed
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {
        if (empty($value)) {
            $this->error(self::ID_INVALID);
            return false;
        }

        $memberManager = $this->options['memberManager'];
        if(!($memberManager instanceof MemberManager)) {
            $this->error(self::ID_INVALID);
            return false;
        }

        try {
            $member = $memberManager->getMember($value);
        } catch (InvalidArgumentException $e) {
            $this->error(self::ID_INVALID);
            return false;
        }
        if ($member->getMemberStatus() != Member::STATUS_ACTIVATED) {
            $this->error(self::ID_INVALID);
            return false;
        }

        return true;
    }


}