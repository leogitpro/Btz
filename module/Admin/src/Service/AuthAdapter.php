<?php
/**
 * Administrator authentication adapter
 *
 * User: leo
 */

namespace Admin\Service;


use Admin\Entity\Adminer;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;


class AuthAdapter implements AdapterInterface
{

    /**
     * @var AdminerManager
     */
    private $adminerManager;

    /**
     * @var string
     */
    private $account;


    /**
     * @var string
     */
    private $password;


    /**
     * AuthAdapter constructor.
     *
     * @param AdminerManager $adminerManager
     */
    public function __construct(AdminerManager $adminerManager)
    {
        $this->adminerManager = $adminerManager;
    }

    /**
     * @return string
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param string $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
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
        $adminer = $this->adminerManager->getAdministratorByEmail($this->getAccount());
        if (null == $adminer) {
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                null,
                ['Invalid identity.']
            );
        }

        if (Adminer::STATUS_RETRIED == $adminer->getAdminStatus()) {
            return new Result(
                Result::FAILURE_UNCATEGORIZED,
                null,
                ['Administrator was retired.']
            );
        }

        if ($this->getPassword() == $adminer->getAdminPasswd()) {
            return new Result(
                Result::SUCCESS,
                $this->getAccount(),
                ['Authenticated successfully.']
            );
        }

        return new Result(
            Result::FAILURE,
            null,
            ['Authenticated failure.']
        );
    }
}