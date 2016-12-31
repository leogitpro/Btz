<?php
/**
 * Administrator service
 *
 * User: leo
 */

namespace Admin\Service;


use Admin\Entity\Adminer;
use Doctrine\ORM\EntityManager;
use Zend\Log\Logger;

class AdminerManager
{

    /**
     * @var EntityManager
     */
    private $entityManager;


    /**
     * @var Logger
     */
    private $logger;


    public function __construct(EntityManager $entityManager, Logger $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }


    /**
     * Get administrator by id
     *
     * @param integer $admin_id
     * @return Adminer
     */
    public function getAdministrator($admin_id)
    {
        return $this->entityManager->getRepository(Adminer::class)->find($admin_id);
    }


    /**
     * Get administrator information by email
     *
     * @param string $email
     * @return Adminer
     */
    public function getAdministratorByEmail($email)
    {
        return $this->entityManager->getRepository(Adminer::class)->findOneBy(['admin_email' => $email]);
    }


    /**
     * Update edited administrator information to database.
     *
     * @param Adminer $adminer
     * @return Adminer
     */
    public function saveUpdatedAdministrator(Adminer $adminer)
    {
        if (null != $adminer) {
            $this->entityManager->persist($adminer);
            $this->entityManager->flush();
        }

        return $adminer;
    }


    /**
     * Update a administrator password
     *
     * @param integer $adminerId
     * @param string $password
     * @return Adminer
     */
    public function updateAdministratorPassword($adminerId, $password)
    {
        $adminer = $this->getAdministrator($adminerId);
        if (null == $adminer) {
            $this->logger->err(__METHOD__ . PHP_EOL . 'Get administrator by id(' . $adminerId . ') failure');
            return false;
        }

        $adminer->setAdminPasswd($password);

        $this->entityManager->persist($adminer);
        $this->entityManager->flush();

        return $adminer;
    }


    /**
     * Update a administrator password
     *
     * @param string $email
     * @param string $password
     * @return Adminer
     */
    public function updateAdministratorPasswordByEmail($email, $password)
    {
        $adminer = $this->getAdministratorByEmail($email);
        if (null == $adminer) {
            $this->logger->err(__METHOD__ . PHP_EOL . 'Get administrator by email(' . $email . ') failure');
            return false;
        }

        $adminer->setAdminPasswd($password);

        $this->entityManager->persist($adminer);
        $this->entityManager->flush();

        return $adminer;
    }








}