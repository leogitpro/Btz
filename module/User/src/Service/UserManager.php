<?php
/**
 * User service manager
 *
 * User: leo
 */

namespace User\Service;


use Doctrine\ORM\EntityManager;
use User\Entity\User;
use Zend\Log\Logger;


class UserManager
{

    /**
     * Doctrine entity manager.
     *
     * @var EntityManager
     */
    private $entityManager;


    /**
     * @var Logger
     */
    private $logger;


    /**
     * UserManager constructor.
     *
     * @param EntityManager $entityManager
     * @param Logger $logger
     */
    public function __construct(EntityManager $entityManager, Logger $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
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

        $this->logger->debug(__METHOD__ . ' added user: ' . $data['email']);

        return $newUser;
    }


    /**
     * @param string $code
     * @return User
     */
    public function activeUser($code)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneByActiveToken($code);
        if (null == $user) {
            $this->logger->info(__METHOD__ . PHP_EOL . 'Invalid active code:' . $code . ' for active user');
            return false;
        }

        if (User::STATUS_ACTIVE == $user->getStatus()) {
            $this->logger->info(__METHOD__ . PHP_EOL . 'User is activated for active code:' . $code . ' no need active again');
            return false;
        }

        $user->setStatus(User::STATUS_ACTIVE);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->logger->debug(__METHOD__ . PHP_EOL . 'User[' . $user->getEmail() . '] has been activated by code: ' . $code);

        return $user;
    }


    /**
     * Reset password token
     *
     * @param string $email
     * @return User
     */
    public function resetUserPasswordToken($email)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneByEmail($email);
        if (null == $user) {
            $this->logger->info(__METHOD__ . PHP_EOL . 'Invalid user email:' . $email . ' for reset pwd token');
            return false;
        }

        $token = md5($user->getEmail() . time());

        $user->setPwdResetToken($token);
        $user->setPwdResetTokenCreated(time());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->logger->debug(__METHOD__ . PHP_EOL . 'User[' . $user->getEmail() . '] has reset password token');

        return $user;
    }

}