<?php
/**
 * EntityManager root class
 *
 * User: leo
 */

namespace Admin\Service;


use Doctrine\ORM\EntityManager;


class BaseEntityManager
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    private $qb = null;


    /**
     * BaseEntityManager constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        $this->qb = $this->entityManager->createQueryBuilder();
    }


    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getQb()
    {
        if (null === $this->qb) {
            $this->qb = $this->entityManager->createQueryBuilder();
        }
        return $this->qb;
    }


    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function resetQb()
    {
        $this->getQb()->getParameters()->clear();
        $this->getQb()->resetDQLParts();
        return $this->getQb();
    }


    /**
     * @return int
     */
    protected function getEntitiesCount()
    {
        return $this->getQb()->getQuery()->getSingleScalarResult();
    }


    /**
     * @return array
     */
    protected function getEntitiesFromPersistence()
    {
        return $this->getQb()->getQuery()->getResult();
    }


    /**
     * @return mixed
     */
    protected function getEntityFromPersistence()
    {
        return $this->getQb()->getQuery()->getOneOrNullResult();
    }


    /**
     * @param object $entity
     */
    public function saveModifiedEntity($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }


    /**
     * @param array $entities
     */
    public function saveModifiedEntities($entities)
    {
        foreach ($entities as $entity) {
            $this->entityManager->persist($entity);
        }
        $this->entityManager->flush();
    }


    /**
     * @param object $entity
     */
    public function removeEntity($entity)
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }


    /**
     * @param array $entities
     */
    public function removeEntities($entities)
    {
        foreach ($entities as $entity) {
            $this->entityManager->remove($entity);
        }

        $this->entityManager->flush();
    }


}