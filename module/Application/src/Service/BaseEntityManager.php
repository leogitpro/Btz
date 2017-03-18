<?php
/**
 * BaseEntityManager.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Application\Service;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Logger\Service\LoggerService;


class BaseEntityManager
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var LoggerService
     */
    protected $logger;


    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    private $qb = null;


    /**
     * BaseEntityManager constructor.
     *
     * @param EntityManager $entityManager
     * @param LoggerService $logger
     */
    public function __construct(EntityManager $entityManager, LoggerService $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;

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
        try {
            return $this->getQb()->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            $this->logger->err(__METHOD__ . PHP_EOL . $e->getMessage());
        } catch (NonUniqueResultException $e) {
            $this->logger->err(__METHOD__ . PHP_EOL . $e->getMessage());
        }

        return 0;
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
        try {
            return $this->getQb()->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            $this->logger->err(__METHOD__ . PHP_EOL . $e->getMessage());
            return null;
        }
    }


    /**
     * @param object $entity
     */
    public function saveModifiedEntity($entity)
    {
        try {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        } catch (ORMInvalidArgumentException $e) {
            $this->logger->err(__METHOD__ . PHP_EOL . $e->getMessage());
        } catch (ORMException $e) {
            $this->logger->err(__METHOD__ . PHP_EOL . $e->getMessage());
        }
    }


    /**
     * @param array $entities
     */
    public function saveModifiedEntities($entities)
    {
        foreach ($entities as $entity) {
            try {
                $this->entityManager->persist($entity);
            } catch (ORMInvalidArgumentException $e) {
                $this->logger->err(__METHOD__ . PHP_EOL . $e->getMessage());
            } catch (ORMException $e) {
                $this->logger->err(__METHOD__ . PHP_EOL . $e->getMessage());
            }
        }

        try {
            $this->entityManager->flush();
        } catch (OptimisticLockException $e) {
            $this->logger->err(__METHOD__ . PHP_EOL . $e->getMessage());
        }
    }


    /**
     * @param object $entity
     */
    public function removeEntity($entity)
    {
        try {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
        } catch (ORMInvalidArgumentException $e) {
            $this->logger->err(__METHOD__ . PHP_EOL . $e->getMessage());
        } catch (ORMException $e) {
            $this->logger->err(__METHOD__ . PHP_EOL . $e->getMessage());
        }
    }


    /**
     * @param array $entities
     */
    public function removeEntities($entities)
    {
        foreach ($entities as $entity) {
            try {
                $this->entityManager->remove($entity);
            } catch (ORMInvalidArgumentException $e) {
                $this->logger->err(__METHOD__ . PHP_EOL . $e->getMessage());
            } catch (ORMException $e) {
                $this->logger->err(__METHOD__ . PHP_EOL . $e->getMessage());
            }
        }

        try {
            $this->entityManager->flush();
        } catch (OptimisticLockException $e) {
            $this->logger->err(__METHOD__ . PHP_EOL . $e->getMessage());
        }
    }


}