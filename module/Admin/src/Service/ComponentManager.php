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
use Admin\Exception\InvalidArgumentException;
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
     * @throws InvalidArgumentException
     */
    public function getComponent($component_class)
    {
        $this->resetQb();

        $this->getQb()->from(Component::class, 't')->select('t');
        $this->getQb()->where($this->getQb()->expr()->eq('t.comClass', '?1'));
        $this->getQb()->setParameter(1, $component_class);

        $obj = $this->getEntityFromPersistence();
        if (!$obj instanceof Component) {
            throw new InvalidArgumentException('无效的组件名');
        }
        return $obj;
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
     * @throws InvalidArgumentException
     */
    public function getAction($action_id)
    {
        $this->resetQb();

        $this->getQb()->from(Action::class, 't')->select('t');
        $this->getQb()->where($this->getQb()->expr()->eq('t.actionId', '?1'));
        $this->getQb()->setParameter(1, $action_id);

        $obj = $this->getEntityFromPersistence();
        if (!$obj instanceof Action) {
            throw new InvalidArgumentException('无效的接口编号');
        }
        return $obj;
    }


    /**
     * Get all valid menu components
     *
     * @return array
     */
    public function getComponentsForAutoMenu()
    {
        $this->resetQb();

        $this->getQb()->select('t')->from(Component::class, 't');
        $this->getQb()->where($this->getQb()->expr()->eq('t.comMenu', '?1'));
        $this->getQb()->setParameter(1, Component::MENU_YES);
        $this->getQb()->setMaxResults(200)->setFirstResult(0);
        $this->getQb()->orderBy('t.comRank', 'DESC')->addOrderBy('t.comMenu', 'DESC')->addOrderBy('t.comName');

        return $this->getEntitiesFromPersistence();
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

        // Remove drop component, manual delete from the backend system.
        // Will keep the acl system clean.
        /**
        foreach ($savedComponents as $k => $component) {
            if (!array_key_exists($k, $existed)) {
                if ($component instanceof Component) {
                    // Delete there acl

                    // Delete component with all actions
                    $this->removeEntity($component);
                }
            }
        }
        //*/


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

                            // Manual delete from the backend system. will keep the acl clean.
                            //$this->removeEntity($action);

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

}