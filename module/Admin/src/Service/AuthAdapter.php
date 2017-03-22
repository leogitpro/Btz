<?php
/**
 * Administrator authentication adapter
 *
 * User: leo
 */

namespace Admin\Service;

use Admin\Entity\Member;
use Admin\Exception\InvalidArgumentException;
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
     * @throws InvalidArgumentException
     */
    public function getMemberByEmail($email)
    {
        $qb = $this->resetQb();

        $qb->from(Member::class, 't')->select('t');
        $qb->where($qb->expr()->eq('t.memberEmail', '?1'));
        $qb->setParameter(1, $email);

        $member = $qb->getQuery()->getOneOrNullResult();
        if (!$member instanceof Member) {
            throw new InvalidArgumentException('无效的账号信息: ' . $email);
        }
        return $member;
    }


    /**
     * Authenticate administrator login
     *
     * @return Result
     */
    public function authenticate()
    {
        try {
            $member = $this->getMemberByEmail($this->getEmail());
        } catch (InvalidArgumentException $e) {
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

        $expired = $member->getMemberExpired();
        if(date('Ymd') > $expired->format('Ymd')) {
            return new Result(
                Result::FAILURE,
                null,
                ['Expired']
            );
        }

        return new Result(
            Result::SUCCESS,
            $member->getMemberId(),
            ['Authenticated successfully.']
        );
    }
}