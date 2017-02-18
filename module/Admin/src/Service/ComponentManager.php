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
use Ramsey\Uuid\Uuid;

class ComponentManager extends BaseEntityManager
{


    /**
     * Get all components count
     *
     * @return integer
     */
    public function getComponentsCount()
    {
        $this->resetQb();

        $this->getQb()->select($this->getQb()->expr()->count('t.comClass'));
        $this->getQb()->from(Component::class, 't');

        return $this->getEntitiesCount();
    }


    /**
     * Get one component
     *
     * @param string $component_class
     * @return Component
     */
    public function getComponent($component_class)
    {
        $this->resetQb();

        $this->getQb()->from(Component::class, 't')->select('t');
        $this->getQb()->where($this->getQb()->expr()->eq('t.comClass', '?1'));
        $this->getQb()->setParameter(1, $component_class);

        return $this->getEntityFromPersistence();
    }


    /**
     * Get components by limit page
     *
     * @param integer $page
     * @param integer $size
     * @return array
     */
    public function getComponentsByLimitPage($page = 1, $size = 100)
    {
        $this->resetQb();

        $this->getQb()->select('t')->from(Component::class, 't');
        $this->getQb()->setMaxResults($size)->setFirstResult(($page -1) * $size);
        $this->getQb()->orderBy('t.comRank', 'DESC')->addOrderBy('t.comMenu', 'DESC')->addOrderBy('t.comName');

        return $this->getEntitiesFromPersistence();
    }


    /**
     * Get all components
     *
     * @return array
     */
    public function getAllComponents()
    {
        return $this->getComponentsByLimitPage(1, 200);
    }



    /**
     * Get a action by id
     *
     * @param string $action_id
     * @return Action
     */
    public function getAction($action_id)
    {
        $this->resetQb();

        $this->getQb()->from(Action::class, 't')->select('t');
        $this->getQb()->where($this->getQb()->expr()->eq('t.actionId', '?1'));
        $this->getQb()->setParameter(1, $action_id);

        return $this->getEntityFromPersistence();
    }




    // Todo



    /**
     * Get all valid menu components
     *
     * @return array
     */
    public function getComponentsForAutoMenu()
    {
        return $this->getUniverseComponents([
            'comStatus' => Component::STATUS_VALIDITY,
            'comMenu' => Component::MENU_YES,
        ], null, 200);
    }


    /**
     * Get some valid components
     *
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getComponentsByLimitPagex($page = 1, $size = 10)
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
     * Get a component all valid actions
     *
     * @param Component $component
     * @return array
     */
    public function getComponentActions(Component $component)
    {
        return $this->getUniverseActions([
            'controllerClass' => $component->getComClass(),
            'actionStatus' => Action::STATUS_VALIDITY,
        ]);
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
            $orders = ['actionStatus' => 'DESC', 'actionRank' => 'DESC', 'actionMenu' => 'DESC', 'actionName' => 'ASC'];
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
     * Sync all components to registry
     *
     * @param array $items
     * @return bool
     */
    public function syncComponents(array $items)
    {

        $components = $this->getAllComponents();

        $savedComponents = [];
        foreach ($components as $component) {
            if ($component instanceof Component) {
                $savedComponents[$component->getComClass()] = $component;
            }
        }

        $increased = [];
        $existed = [];
        foreach ($items as $item) {
            if (array_key_exists($item['controller'], $savedComponents)) {
                $existed[$item['controller']] = $item;
            } else {
                $increased[$item['controller']] = $item;
            }
        }

        // Remove drop component
        foreach ($savedComponents as $k => $component) {
            if (!array_key_exists($k, $existed)) {
                if ($component instanceof Component) {
                    // Delete there acl

                    // Delete component with all actions
                    $this->removeEntity($component);
                }
            }
        }


        // Sync increased component
        foreach ($increased as $k => $item) {
            $component = new Component();
            $component->setComClass($item['controller']);
            $component->setComName($item['name']);
            $component->setComRoute($item['route']);
            $component->setComMenu($item['menu']);
            $component->setComIcon($item['icon']);
            $component->setComRank($item['rank']);

            foreach ($item['actions'] as $sub) {
                $action = new Action();
                $action->setActionId(Uuid::uuid1()->toString());
                $action->setActionKey($sub['action']);
                $action->setActionName($sub['name']);
                $action->setActionMenu($sub['menu']);
                $action->setActionIcon($sub['icon']);
                $action->setActionRank($sub['rank']);

                $action->setComponent($component);
                $component->getActions()->add($action);
            }

            $this->saveModifiedEntity($component);
        }


        // Sync existed component
        foreach ($existed as $k => $item) {
            $component = $savedComponents[$k];
            if ($component instanceof Component) {
                $savedActions = $component->getActions();

                $existedActions = [];
                foreach ($savedActions as $action) {
                    if ($action instanceof Action) {
                        if (array_key_exists($action->getActionKey(), $item['actions'])) {
                            $existedActions[$action->getActionKey()] = $action;
                        } else {
                            //$this->logger->debug('remove action: ' . $action->getActionName() . ' from component: ' . $component->getComName());
                            $this->removeEntity($action);
                        }
                    }
                }

                // Increased actions
                foreach ($item['actions'] as $key => $sub) {
                    if (!array_key_exists($key, $existedActions)) {
                        $action = new Action();
                        $action->setActionId(Uuid::uuid1()->toString());
                        $action->setActionKey($sub['action']);
                        $action->setActionName($sub['name']);
                        $action->setActionMenu($sub['menu']);
                        $action->setActionIcon($sub['icon']);
                        $action->setActionRank($sub['rank']);

                        $action->setComponent($component);
                        $component->getActions()->add($action);

                        //$this->saveModifiedEntity($action);
                        //$this->logger->debug('increased action: ' . $action->getActionName() . ' for component: ' . $component->getComName());
                    }
                }

                // Update the existed actions
                foreach ($existedActions as $key => $action) {
                    if ($action instanceof Action) {
                        $sub = $item['actions'][$key];
                        $action->setActionName($sub['name']);
                        $action->setActionMenu($sub['menu']);
                        $action->setActionIcon($sub['icon']);
                        $action->setActionRank($sub['rank']);
                        $this->saveModifiedEntity($action);
                    }
                }

                // Update the component
                $component->setComName($item['name']);
                $component->setComRoute($item['route']);
                $component->setComMenu($item['menu']);
                $component->setComIcon($item['icon']);
                $component->setComRank($item['rank']);

                $this->saveModifiedEntity($component);
            }
        }


        return true;
    }


    /**
     * Sync all component information
     *
     * @param array $items
     * @return bool
     */
    public function syncComponents1($items)
    {
        if (empty($items)) {
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