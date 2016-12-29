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
        return $this->entityManager->getRepository(Adminer::class)->findOneBy(['adminEmail' => $email]);
    }



}