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


class AuthAdapter extends BaseEntityManager implements AdapterInterface
{

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;



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
     * @param string $email
     * @return Member
     */
    public function getMemberByEmail($email)
    {
        return $this->entityManager->getRepository(Member::class)->findOneBy(['member_email' => $email]);
    }


    /**
     * Authenticate administrator login
     *
     * @return Result
     */
    public function authenticate()
    {
        $member = $this->getMemberByEmail($this->getEmail());
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