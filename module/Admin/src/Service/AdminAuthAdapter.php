<?php
/**
 * Administrator authentication adapter
 *
 * User: leo
 */

namespace Admin\Service;


use Doctrine\ORM\EntityManager;
use Zend\Authentication\Adapter\AdapterInterface;


class AdminAuthAdapter implements AdapterInterface
{

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var string
     */
    private $account;


    /**
     * @var string
     */
    private $password;


    /**
     * AdminAuthAdapter constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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


    public function authenticate()
    {
        // TODO: Implement authenticate() method.
    }
}