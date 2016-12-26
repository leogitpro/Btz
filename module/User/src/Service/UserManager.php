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
     * Get user by id
     *
     * @param integer $uid
     * @return User
     */
    public function getUserById($uid)
    {
        return $this->entityManager->getRepository(User::class)->find($uid);
    }


    /**
     * Get user by field: email
     *
     * @param string $email
     * @return User
     */
    public function getUserByEmail($email)
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    }


    /**
     * Get user by field: active_token
     *
     * @param string $token
     * @return User
     */
    public function getUserByActiveToken($token)
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['activeToken' => $token]);
    }


    /**
     * Get user by filed: pwd_reset_token
     *
     * @param string $token
     * @return User
     */
    public function getUserByResetPwdToken($token)
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['pwdResetToken' => $token]);
    }


    /**
     * Direct save user all attributes.
     * Take care for this api
     *
     * @param User $user
     * @return User
     */
    public function saveEditedUser($user)
    {
        if ($user instanceof User) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
        return $user;
    }


    /**
     * Update user password
     *
     * @param string $password
     * @param string $email
     * @return User
     */
    public function updateUserPasswordByEmail($password, $email)
    {
        $user = $this->getUserByEmail($email);
        if (null == $user) {
            return false;
        }

        $user->setPasswd($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }


    /**
     * Update user email
     *
     * @param User $user
     * @param string $new_email
     * @return User
     */
    public function updateUserEmail(User $user, $new_email)
    {
        $user->setEmail($new_email);
        $user->setActiveToken(md5($new_email . time()));
        $user->setStatus(User::STATUS_RETIRED);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }


    /**
     * Update password by reset password token
     *
     * @param $token
     * @param $password
     * @return User
     */
    public function resetPasswordByToken($token, $password)
    {
        $user = $this->getUserByResetPwdToken($token);
        if (!$user) {
            $this->logger->err(__METHOD__  . PHP_EOL . 'Invalid pwd_reset_token for get user information: ' . $token);
            return false;
        }

        $user->setPasswd($password);
        $user->setPwdResetTokenCreated(0);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
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

        $activeToken = md5($data['email'] . time());
        $newUser->setActiveToken($activeToken);

        $this->entityManager->persist($newUser); // Add entity to the entity manager.
        $this->entityManager->flush(); // Apply changes to database.

        $this->logger->debug(__METHOD__ . PHP_EOL . ' added user: ' . $data['email']);

        return $newUser;
    }


    /**
     * @param string $code
     * @return User
     */
    public function activeUser($code)
    {
        $user = $this->getUserByActiveToken($code);
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
        $user = $this->getUserByEmail($email);
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