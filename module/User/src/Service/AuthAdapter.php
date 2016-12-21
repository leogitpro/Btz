<?php
/**
 * Custom authentication adapter
 *
 * User: leo
 */

namespace User\Service;


use Doctrine\ORM\EntityManager;
use User\Entity\User;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

class AuthAdapter implements AdapterInterface
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
     * @var EntityManager
     */
    private $entityManager;



    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = strtolower($password);
    }


    /**
     * @return mixed
     */
    public function authenticate()
    {
        //check user table select user by email
        $user = $this->entityManager->getRepository(User::class)->findOneByEmail($this->email);

        // Invalid email address
        if (null == $user) {
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                null,
                ['Invalid identity.']
            );
        }

        // Check user status is retired
        if (User::STATUS_RETIRED == $user->getStatus()) {
            return new Result(
                Result::FAILURE,
                null,
                ['User is retired.']
            );
        }

        // Check user password to pass authenticated
        if($this->password == $user->getPasswd()) {
            return new Result(
                Result::SUCCESS,
                $this->email,
                ['Authenticated successfully.']
            );
        }

        // Default auth failure
        return new Result(
            Result::FAILURE_CREDENTIAL_INVALID,
            null,
            ['Invalid credentials.']
        );
    }


}