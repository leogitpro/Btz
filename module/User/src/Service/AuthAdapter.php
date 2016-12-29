<?php
/**
 * User authentication adapter
 *
 * User: leo
 */

namespace User\Service;


use User\Entity\User;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

class AuthAdapter implements AdapterInterface
{

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $password;


    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
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
     * authenticate user identity
     *
     * @return Result
     */
    public function authenticate()
    {
        $user = $this->userManager->getUserByEmail($this->getEmail());
        if (null == $user) {
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                null,
                ['Invalid identity.']
            );
        }

        if(User::STATUS_RETIRED == $user->getStatus()) {
            return new Result(
                Result::FAILURE,
                null,
                ['User is retired.']
            );
        }

        if($this->getPassword() == $user->getPasswd()) {
            return new Result(
                Result::SUCCESS,
                $this->email,
                ['Authenticated successfully.']
            );
        }

        return new Result(
            Result::FAILURE_CREDENTIAL_INVALID,
            null,
            ['Invalid credentials.']
        );
    }

}