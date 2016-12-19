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


    /**
     * Create new user
     *
     * @param array $data
     * @return User
     */
    public function addNewUser($data)
    {
        $newUser = new User();

        $newUser->setEmail($data['email']);
        $newUser->setName($data['name']);
        $newUser->setPasswd($data['passwd']);
        $newUser->setStatus(User::STATUS_RETIRED); // New user need active email manually
        $newUser->setCreated(date('Y-m-d H:i:s'));

        $activeToken = md5($data['email'] . $data['passwd']);
        $newUser->setActiveToken($activeToken);

        $this->entityManager->persist($newUser); // Add entity to the entity manager.

        $this->entityManager->flush(); // Apply changes to database.

        return $newUser;
    }

}