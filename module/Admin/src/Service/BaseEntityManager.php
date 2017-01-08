<?php
/**
 * EntityManager root class
 *
 * User: leo
 */

namespace Admin\Service;


use Doctrine\ORM\EntityManager;
use Zend\Log\Logger;

class BaseEntityManager
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Logger
     */
    protected $logger;


    /**
     * BaseEntityManager constructor.
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
     * Simple count entities
     *
     * @param string $entity
     * @param string $countedField
     * @return integer
     */
    protected function getEntitiesCount($entity, $countedField)
    {
        $qb = $this->entityManager->getRepository($entity)->createQueryBuilder('t');
        return $qb->select('count(t.' . $countedField . ')')->getQuery()->getSingleScalarResult();
    }


    /**
     * @param mixed $entity
     * @return mixed
     */
    protected function saveModifiedEntity($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }


}