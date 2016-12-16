<?php
/**
 * User service manager
 *
 * User: leo
 */

namespace User\Service;



use User\Entity\User;



class UserManager
{

    /**
     * Doctrine entity manager.
     *
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;


    /**
     * UserManager constructor.
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

}