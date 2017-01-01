<?php
/**
 * Administrator authentication adapter
 *
 * User: leo
 */

namespace Admin\Service;

use Admin\Entity\Member;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;


class AuthAdapter implements AdapterInterface
{

    /**
     * @var MemberManager
     */
    private $memberManager;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;



    public function __construct(MemberManager $memberManager)
    {
        $this->memberManager = $memberManager;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }


    /**
     * Authenticate administrator login
     *
     * @return Result
     */
    public function authenticate()
    {
        $member = $this->memberManager->getMemberByEmail($this->getEmail());
        if (null == $member) {
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                null,
                ['Invalid identity.']
            );
        }

        if (Member::STATUS_ACTIVATED != $member->getMemberStatus()) {
            return new Result(
                Result::FAILURE_UNCATEGORIZED,
                null,
                ['Unactivated identity.']
            );
        }

        if ($this->getPassword() != $member->getMemberPassword()) {
            return new Result(
                Result::FAILURE_CREDENTIAL_INVALID,
                null,
                ['Password is incorrect.']
            );
        }

        return new Result(
            Result::SUCCESS,
            $member->getMemberId(),
            ['Authenticated successfully.']
        );
    }
}