<?php
/**
 * ComponentManager.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Service;


use Admin\Entity\Component;

class ComponentManager extends BaseEntityManager
{


    /**
     * @return integer
     */
    public function getComponentsCount()
    {
        return $this->getEntitiesCount(Component::class, 'comId');
    }


    /**
     * Get all components
     *
     * @return array
     */
    public function getAllComponents()
    {
        return $this->getUniverseComponents();
    }


    /**
     * Get all components by limit page
     *
     * @param integer $page
     * @param integer $size
     * @return array
     */
    public function getAllComponentsByLimitPage($page = 1, $size = 10)
    {
        return $this->getUniverseComponents([], null, $size, ($page - 1) * $size );
    }


    /**
     * Get all valid components
     *
     * @return array
     */
    public function getComponents()
    {
        return $this->getUniverseComponents([
            'comStatus' => Component::STATUS_VALIDITY,
        ]);
    }


    /**
     * Get some valid components
     *
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getComponentsByLimitPage($page = 1, $size = 10)
    {
        return $this->getUniverseComponents([
            'comStatus' => Component::STATUS_VALIDITY,
        ], null, $size, ($page - 1) * $size );
    }


    /**
     * Get some components.
     * By default max records limit to 100
     *
     * @param array $criteria
     * @param array|null $orders
     * @param int $limit
     * @param int $offset
     * @return array
     */
    private function getUniverseComponents($criteria = [], $orders = null, $limit = 100, $offset = 0)
    {
        if (null == $orders) {
            $orders = [
                'comRank' => 'DESC',
                'comName' => 'ASC',
            ];
        }
        return $this->entityManager->getRepository(Component::class)->findBy($criteria, $orders, $limit, $offset);
    }


    /**
     * Get component by class name
     *
     * @param string $class
     * @return Component
     */
    public function getComponentByClass($class)
    {
        return $this->getUniverseComponent(['comClass' => $class]);
    }


    /**
     * Get a entity
     *
     * @param array $criteria
     * @param array|null $orders
     * @return object
     */
    private function getUniverseComponent($criteria, $orders = null)
    {
        return $this->entityManager->getRepository(Component::class)->findOneBy($criteria, $orders);
    }


    /**
     * Sync all component information
     *
     * @param array $items
     * @return bool
     */
    public function syncComponents($items)
    {
        if (empty($items)) {
            return false;
        }

        foreach($items as $item) {
            $existed = $this->getComponentByClass($item['controller']);
            if ($existed instanceof Component) {
                // check actions
            } else {
                // New component
                $entity = new Component();
                $entity->setComClass($item['controller']);
                $entity->setComName($item['name']);
                $entity->setComRoute($item['route']);
                $entity->setComStatus(Component::STATUS_VALIDITY);
                $entity->setComCreated(new \DateTime());

                if (isset($item['menu'])) {
                    $entity->setComMenu($item['menu']);
                }
                if (isset($item['icon'])) {
                    $entity->setComIcon($item['icon']);
                }
                if (isset($item['rank'])) {
                    $entity->setComRank($item['rank']);
                }

                $this->entityManager->persist($entity);
            }
        }

        $this->entityManager->flush();

        return true;
    }

}