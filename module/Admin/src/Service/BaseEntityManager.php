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
     * $where = ['status => ?', 'age = ?'];
     * $params = [0 => 1, 1 => 38];
     *
     * @param string $entity
     * @param string $countedField
     * @param array $where
     * @param array $params
     * @param string $alias
     * @return integer
     */
    protected function getEntitiesCount($entity, $countedField, $where = [], $params = [], $alias = 't')
    {
        $qb = $this->entityManager->getRepository($entity)->createQueryBuilder($alias);
        $qb->select('count(' . $alias . '.' . $countedField . ')');
        if (!empty($where)) {
            foreach ($where as $p) {
                $qb->where($alias . '.' . $p);
            }
            if (!empty($params)) {
                foreach ($params as $k => $v) {
                    $qb->setParameter($k, $v);
                }
            }
        }
        //var_dump($qb->getDQL());
        return $qb->getQuery()->getSingleScalarResult();
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