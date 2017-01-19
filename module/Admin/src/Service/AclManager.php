<?php
/**
 * AclManager.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Service;


use Admin\Entity\AclDepartment;
use Admin\Entity\AclMember;
use Admin\Entity\Action;
use Admin\Entity\Component;
use Admin\Entity\DepartmentMember;
use Admin\Entity\Member;
use Doctrine\ORM\EntityManager;
use Zend\Log\Logger;

class AclManager extends BaseEntityManager
{

    /**
     * @var MemberManager
     */
    private $memberManager;

    /**
     * @var ComponentManager
     */
    private $componentManager;


    /**
     * @var DMRelationManager
     */
    private $dmrManager;


    public function __construct(
        MemberManager $memberManager,
        ComponentManager $componentManager,
        DMRelationManager $dmrManager,
        EntityManager $entityManager,
        Logger $logger
    )
    {
        parent::__construct($entityManager, $logger);

        $this->componentManager = $componentManager;
        $this->memberManager = $memberManager;
        $this->dmrManager = $dmrManager;
    }


    /**
     * @param int $memberId
     * @return array
     */
    private function getMemberMergedAcls($memberId)
    {
        $forbiddenActionIds = [];
        $allowedActionIds = [];

        $rows = $this->getMemberAllAcls($memberId);
        foreach ($rows as $acl) {
            if ($acl instanceof AclMember) {
                if (AclMember::STATUS_FORBIDDEN == $acl->getStatus()) {
                    $forbiddenActionIds[$acl->getActionId()] = $acl->getActionId();
                }
                if (AclMember::STATUS_ALLOWED == $acl->getStatus()) {
                    $allowedActionIds[$acl->getActionId()] = $acl->getActionId();
                }
            }
        }

        $this->logger->debug('Member personal forbidden access actions:' . implode('-', $forbiddenActionIds));
        $this->logger->debug('Member personal allowed access actions:' . implode('-', $allowedActionIds));

        // Member departments
        $deptIds = [];
        $relations = $this->dmrManager->memberRelations($memberId);
        foreach ($relations as $relation) {
            if ($relation instanceof DepartmentMember) {
                $deptIds[$relation->getDeptId()] = $relation->getDeptId();
            }
        }
        $this->logger->debug('My belong departments:' . implode('-', $deptIds));

        // Department acls
        foreach ($deptIds as $deptId) {
            $rows = $this->getDepartmentAcls($deptId);
            foreach ($rows as $acl) {
                if ($acl instanceof AclDepartment) {
                    $allowedActionIds[$acl->getActionId()] = $acl->getActionId();
                }
            }
        }

        $actionIds = [];
        foreach ($allowedActionIds as $id) {
            if (!in_array($id, $forbiddenActionIds)) {
                $actionIds[$id] = $id;
            }
        }
        $this->logger->debug("The final can access actions: " . implode('-', $actionIds));

        return [
            'allowed' => $actionIds,
            'forbidden' => $forbiddenActionIds,
        ];
    }


    /**
     * @param int $memberId
     * @param string $controllerClass
     * @param string $actionKey
     * @return bool
     */
    public function isValid($memberId, $controllerClass, $actionKey)
    {
        $member = $this->memberManager->getMember($memberId);
        if (null == $member || Member::STATUS_ACTIVATED != $member->getMemberStatus()) {
            return false;
        }

        if (Member::LEVEL_SUPERIOR == $member->getMemberLevel()) { // For supper administrator
            return true;
        }

        $component = $this->componentManager->getComponentByClass($controllerClass);
        if (null == $component || Component::STATUS_VALIDITY != $component->getComStatus()) {
            return false;
        }

        $action = $this->componentManager->getComponentAction($controllerClass, $actionKey);
        if (null == $action || Action::STATUS_VALIDITY != $action->getActionStatus()) {
            return false;
        }
        $actionId = $action->getActionId();

        $acl = $this->getMemberMergedAcls($memberId);
        if (in_array($actionId, $acl['forbidden'])) {
            return false;
        }

        if (in_array($actionId, $acl['allowed'])) {
            return true;
        }

        return false;
    }


    /**
     * Get a member custom menus
     *
     * @param int $memberId
     * @return array
     */
    public function getMemberMenus($memberId)
    {
        $acl = $this->getMemberMergedAcls($memberId);
        $actionIds = $acl['allowed'];
        if (empty($actionIds)) {
            return [];
        }

        $actions = [];
        $componentClasses = [];
        $entities = $this->componentManager->getActionsByIds($actionIds);
        foreach ($entities as $entity) {
            if ($entity instanceof Action) {
                if ($entity->getActionStatus() == Action::STATUS_VALIDITY && $entity->getActionMenu() == Action::MENU_YES) {
                    $componentClasses[$entity->getControllerClass()] = $entity->getControllerClass();
                    $actions[$entity->getControllerClass()][$entity->getActionId()] = [
                        'key' => $entity->getActionKey(),
                        'name' => $entity->getActionName(),
                        'icon' => $entity->getActionIcon(),
                    ];
                }
            }
        }

        if (empty($componentClasses)) {
            return [];
        }

        $components = [];
        $entities = $this->componentManager->getComponentsByClasses($componentClasses);
        foreach ($entities as $entity) {
            if ($entity instanceof Component) {
                if (Component::STATUS_VALIDITY == $entity->getComStatus() && Component::MENU_YES == $entity->getComMenu()) {
                    $item = [
                        'class' => $entity->getComClass(),
                        'name' => $entity->getComName(),
                        'icon' => $entity->getComIcon(),
                        'route' => $entity->getComRoute(),
                        'actions' => $actions[$entity->getComClass()],
                    ];
                    $components[] = $item;
                }
            }
        }

        return $components;
    }


    /**
     * Get global menu
     *
     * @return array
     */
    public function getGlobalMenus()
    {
        $menu = [];
        $rows = $this->componentManager->getComponentsForAutoMenu();
        foreach ($rows as $entity) {
            if ($entity instanceof Component) {
                $actions = [];
                $_rows = $this->componentManager->getComponentActions($entity);
                foreach ($_rows as $_entity) {
                    if ($_entity instanceof Action) {
                        if (Action::MENU_YES == $_entity->getActionMenu()) {
                            $actions[] = [
                                'key' => $_entity->getActionKey(),
                                'name' => $_entity->getActionName(),
                                'icon' => $_entity->getActionIcon(),
                            ];
                        }
                    }
                }

                $item = [
                    'class' => $entity->getComClass(),
                    'name' => $entity->getComName(),
                    'icon' => $entity->getComIcon(),
                    'route' => $entity->getComRoute(),
                ];
                if (!empty($actions)) {
                    $item['actions'] = $actions;
                }

                $menu[] = $item;
            }
        }

        return $menu;
    }


    /**
     * Get a acl for member and action
     *
     * @param $member_id
     * @param $action_id
     * @return AclMember
     */
    public function getMemberActionAcl($member_id, $action_id)
    {
        return $this->getMemberUniverseAcl([
            'actionId' => $action_id,
            'memberId' => $member_id,
        ]);
    }

    /**
     * Get a acl for member with action
     *
     * @param array $criteria
     * @param null|array $order
     * @return AclMember
     */
    private function getMemberUniverseAcl($criteria = [], $order = null)
    {
        return $this->entityManager->getRepository(AclMember::class)->findOneBy($criteria, $order);
    }


    /**
     * Get a member all actions, max records: 200
     *
     * @param $member_id
     * @return array
     */
    public function getMemberAllAcls($member_id)
    {
        return $this->getMemberUniverseAcls(['memberId' => $member_id], null, 200);
    }


    /**
     * Get member all allowed acls
     *
     * @param int $member_id
     * @return array
     */
    public function getMemberAllowedAcls($member_id)
    {
        return $this->getMemberUniverseAcls([
            'memberId' => $member_id,
            'status' => AclMember::STATUS_ALLOWED,
        ], null, 200);
    }


    /**
     * Get member all forbidden acls
     *
     * @param int $member_id
     * @return array
     */
    public function getMemberForbiddenAcls($member_id)
    {
        return $this->getMemberUniverseAcls([
            'memberId' => $member_id,
            'status' => AclMember::STATUS_FORBIDDEN,
        ], null, 200);
    }



    /**
     * Get member acl configurations
     *
     * @param array $criteria
     * @param null $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    private function getMemberUniverseAcls($criteria = [], $order = null, $limit = 10, $offset = 0)
    {
        return $this->entityManager->getRepository(AclMember::class)->findBy($criteria, $order, $limit, $offset);
    }


    /**
     * Get a acl for department with action
     *
     * @param $dept_id
     * @param $action_id
     * @return AclDepartment
     */
    public function getDepartmentActionAcl($dept_id, $action_id)
    {
        return $this->getDepartmentUniverseAcl([
            'deptId' => $dept_id,
            'actionId' => $action_id,
        ]);
    }


    /**
     * Get a acl for department
     *
     * @param array $criteria
     * @param null|array $order
     * @return AclDepartment
     */
    private function getDepartmentUniverseAcl($criteria = [], $order = null)
    {
        return $this->entityManager->getRepository(AclDepartment::class)->findOneBy($criteria, $order);
    }



    /**
     * Get a department all valid actions, max records: 200
     *
     * @param integer $dept_id
     * @return array
     */
    public function getDepartmentAcls($dept_id)
    {
        return $this->getDepartmentUniverseAcls([
            'deptId' => $dept_id,
            'status' => AclDepartment::STATUS_ALLOWED,
        ], null, 200);
    }


    /**
     * Get a department all actions, max records: 200
     *
     * @param integer $dept_id
     * @return array
     */
    public function getDepartmentAllAcls($dept_id)
    {
        return $this->getDepartmentUniverseAcls(['deptId' => $dept_id], null, 200);
    }


    /**
     * Get department acl configurations
     *
     * @param array $criteria
     * @param null $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    private function getDepartmentUniverseAcls($criteria = [], $order = null, $limit = 10, $offset = 0)
    {
        return $this->entityManager->getRepository(AclDepartment::class)->findBy($criteria, $order, $limit, $offset);
    }


    /**
     * Save edited member acl
     *
     * @param integer $member_id
     * @param integer $action_id
     * @param integer $status
     * @return AclMember
     */
    public function saveMemberAcl($member_id, $action_id, $status)
    {
        $acl = $this->getMemberActionAcl($member_id, $action_id);
        if (null == $acl) {
            $acl = new AclMember();
            $acl->setActionId($action_id);
            $acl->setMemberId($member_id);
            $acl->setCreated(new \DateTime());
        }
        $acl->setStatus($status);
        return $this->saveModifiedEntity($acl);
    }


    /**
     * Save edited department acl
     *
     * @param integer $dept_id
     * @param integer $action_id
     * @param integer $status
     * @return AclDepartment
     */
    public function saveDepartmentAcl($dept_id, $action_id, $status)
    {
        $acl = $this->getDepartmentActionAcl($dept_id, $action_id);
        if (null == $acl) {
            $acl = new AclDepartment();
            $acl->setActionId($action_id);
            $acl->setDeptId($dept_id);
            $acl->setCreated(new \DateTime());
        }
        $acl->setStatus($status);
        return $this->saveModifiedEntity($acl);
    }

}