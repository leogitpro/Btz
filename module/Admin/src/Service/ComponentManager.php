<?php
/**
 * ComponentManager.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Service;


use Admin\Entity\Action;
use Admin\Entity\Component;

class ComponentManager extends BaseEntityManager
{


    /**
     * Get one component
     *
     * @param int $component_id
     * @return Component
     */
    public function getComponent($component_id)
    {
        return $this->entityManager->getRepository(Component::class)->find($component_id);
    }


    /**
     * @return integer
     */
    public function getAllComponentsCount()
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
     * @return integer
     */
    public function getComponentsCount()
    {
        return $this->getEntitiesCount(Component::class, 'comId', ['comStatus = :status'], ['status' => Component::STATUS_VALIDITY]);
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
     * Get components with it actions
     *
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getComponentsWithActionsByLimitPage($page = 1, $size = 10)
    {
        $entities = $this->getComponentsByLimitPage($page, $size);
        $actions = [];
        foreach ($entities as $entity) {
            if ($entity instanceof Component) {
                $actions[$entity->getComClass()] = $this->getComponentAllActions($entity);
            }
        }
        return [
            'components' => $entities,
            'actions' => $actions,
        ];
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
                'comStatus' => 'DESC',
                'comRank' => 'DESC',
                'comMenu' => 'DESC',
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
     * Get a component all actions
     *
     * @param Component $component
     * @return array
     */
    public function getComponentAllActions(Component $component)
    {
        return $this->getUniverseActions(['controllerClass' => $component->getComClass()]);
    }


    /**
     * Get a component all actions
     *
     * @param array $criteria
     * @param null|array $orders
     * @return array
     */
    private function getUniverseActions($criteria, $orders = null)
    {
        if (null == $orders) {
            $orders = ['actionRank' => 'DESC', 'actionMenu' => 'DESC', 'actionName' => 'ASC'];
        }
        return $this->entityManager->getRepository(Action::class)->findBy($criteria, $orders, 100);
    }


    /**
     * Get components by classes for menu
     *
     * @param array $classes
     * @return array
     */
    public function getComponentsByClasses($classes)
    {
        return $this->entityManager->getRepository(Component::class)->findByComClass(
            $classes,
            ['comRank' => 'DESC', 'comName' => 'ASC']
        );
    }


    /**
     * Get actions by ids for menu
     *
     * @param array $actionIds
     * @return array
     */
    public function getActionsByIds($actionIds)
    {
        return $this->entityManager->getRepository(Action::class)->findByActionId(
            $actionIds,
            ['actionRank' => 'DESC', 'actionName' => 'ASC']
        );
    }


    /**
     * Get a action by id
     *
     * @param integer $action_id
     * @return Action
     */
    public function getAction($action_id)
    {
        return $this->getUniverseAction(['actionId' => $action_id]);
    }


    /**
     * Get the action
     *
     * @param string $controller_class
     * @param string $action_key
     * @return Action
     */
    public function getComponentAction($controller_class, $action_key)
    {
        return $this->getUniverseAction([
            'controllerClass' => $controller_class,
            'actionKey' => $action_key,
        ]);
    }


    /**
     * Get a action
     *
     * @param array $criteria
     * @param null|array $orders
     * @return Action
     */
    private function getUniverseAction($criteria, $orders = null) {
        return $this->entityManager->getRepository(Action::class)->findOneBy($criteria, $orders);
    }


    /**
     * Save edited component entity
     *
     * @param Component $component
     * @return Component
     */
    public function saveModifiedComponent(Component $component)
    {
        return $this->saveModifiedEntity($component);
    }


    /**
     * Save edited action entity
     *
     * @param Action $action
     * @return Action
     */
    public function saveModifiedAction(Action $action) {
        return $this->saveModifiedEntity($action);
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
            $this->logger->info('No component got, stop sync component');
            return false;
        }

        foreach($items as $item) {

            $this->logger->debug('Sync component: ' . $item['controller']);

            $entity = $this->getComponentByClass($item['controller']);
            if ($entity instanceof Component) {
                // Modify component
                $entity->setComName($item['name']);
                $entity->setComRoute($item['route']);

                if (isset($item['menu'])) {
                    $entity->setComMenu($item['menu']);
                } else {
                    $entity->setComMenu(Component::MENU_NO);
                }

                if (isset($item['icon'])) {
                    $entity->setComIcon($item['icon']);
                } else {
                    $entity->setComIcon(Component::ICON_DEFAULT);
                }

                if (isset($item['rank'])) {
                    $entity->setComRank($item['rank']);
                } else {
                    $entity->setComRank(Component::RANK_DEFAULT);
                }

                $this->entityManager->persist($entity);
                $this->logger->debug('The component: ' . $item['controller'] . ' is existed, modify it.');

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
                $this->logger->debug('The component: ' . $item['controller'] . ' is a new component, create it.');
            }
        }
        $this->entityManager->flush();

        $this->logger->debug("Start sync component actions");
        foreach($items as $item) {
            $actions = isset($item['actions']) ? $item['actions'] : [];
            $this->syncActions($actions, $item['controller']);
        }

        return true;
    }


    /**
     * Sync component actions
     *
     * @param array $items
     * @param string $component
     */
    private function syncActions($items, $component)
    {
        $actions = $this->entityManager->getRepository(Action::class)->findBy(['controllerClass' => $component]);
        $this->logger->debug('Sync component:' . $component . ' actions');

        $existedIndexs = [];
        foreach ($actions as $action) {
            if ($action instanceof Action) {
                $actionKey = $action->getActionKey();
                $index = array_search($actionKey, array_column($items, 'action'));

                if (false !== $index) { // searched, modified action data. skip status.
                    $item = $items[$index];
                    $existedIndexs[] = $index;
                    $action->setActionName($item['name']);

                    if (isset($item['menu']) && $item['menu']) {
                        $action->setActionMenu(Action::MENU_YES);
                    } else {
                        $action->setActionMenu(Action::MENU_NO);
                    }

                    if (isset($item['icon'])) {
                        $action->setActionIcon($item['icon']);
                    } else {
                        $action->setActionIcon(Action::ICON_DEFAULT);
                    }

                    if (isset($item['rank'])) {
                        $action->setActionRank($item['rank']);
                    } else {
                        $action->setActionRank(Action::RANK_DEFAULT);
                    }

                    $this->entityManager->persist($action);
                    $this->logger->debug('The action: ' . $actionKey . ' is existed, modify it.');

                } else { // has removed action, set to invalid
                    $action->setActionStatus(Action::STATUS_INVALID);
                    $this->entityManager->persist($action);
                    $this->logger->debug('The action: ' . $actionKey . ' was removed, set it to invalid.');
                }
            }
        }
        $this->entityManager->flush();

        foreach ($items as $key => $item) {
            if (in_array($key, $existedIndexs)) {
                unset($items[$key]);
            }
        }

        if (count($items) < 1) {
            return ;
        }

        $this->logger->debug('Start create new actions for component: ' . $component);
        foreach ($items as $item) {
            $action = new Action();
            $action->setControllerClass($component);
            $action->setActionKey($item['action']);
            $action->setActionName($item['name']);

            if (isset($item['menu']) && $item['menu']) {
                $action->setActionMenu(Action::MENU_YES);
            } else {
                $action->setActionMenu(Action::MENU_NO);
            }

            if (isset($item['icon'])) {
                $action->setActionIcon($item['icon']);
            } else {
                $action->setActionIcon(Action::ICON_DEFAULT);
            }

            if (isset($item['rank'])) {
                $action->setActionRank($item['rank']);
            } else {
                $action->setActionRank(Action::RANK_DEFAULT);
            }

            $action->setActionStatus(Action::STATUS_VALIDITY);
            $action->setActionCreated(new \DateTime());

            $this->entityManager->persist($action);
            $this->logger->debug('created new action: ' . $item['action']);
        }
        $this->entityManager->flush();

        return ;
    }

}