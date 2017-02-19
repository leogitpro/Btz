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
use Admin\Entity\Department;
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
     * @var
     */
    private $dmrManager;


    public function __construct(
        MemberManager $memberManager,
        ComponentManager $componentManager,
        EntityManager $entityManager,
        Logger $logger
    )
    {
        parent::__construct($entityManager, $logger);

        $this->componentManager = $componentManager;
        $this->memberManager = $memberManager;
    }


    /**
     * Get all acl, max records: 200
     *
     * @param string $action
     * @return array
     */
    public function getMemberAndActionAllAclByAction($action)
    {
        $this->resetQb();

        $this->getQb()->select('t')->from(AclMember::class, 't');
        $this->getQb()->where($this->getQb()->expr()->eq('t.action', '?1'));
        $this->getQb()->setParameter(1, $action);

        $this->getQb()->setMaxResults(200)->setFirstResult(0);

        return $this->getEntitiesFromPersistence();
    }



    /**
     * Get a member all actions, max records: 200
     *
     * @param string $member_id
     * @return array
     */
    public function getMemberAndActionAllAclByMember($member_id)
    {
        $this->resetQb();

        $this->getQb()->select('t')->from(AclMember::class, 't');
        $this->getQb()->where($this->getQb()->expr()->eq('t.member', '?1'));
        $this->getQb()->setParameter(1, $member_id);

        $this->getQb()->setMaxResults(200)->setFirstResult(0);

        return $this->getEntitiesFromPersistence();
    }


    /**
     * Get a record for member and action
     *
     * @param string $member
     * @param string $action
     * @return AclMember
     */
    public function getMemberAndActionAcl($member, $action)
    {
        $this->resetQb();

        $this->getQb()->select('t')->from(AclMember::class, 't');
        $this->getQb()->where(
            $this->getQb()->expr()->andX(
                $this->getQb()->expr()->eq('t.member', '?1'),
                $this->getQb()->expr()->eq('t.action', '?2')
            )
        );
        $this->getQb()->setParameter(1, $member)->setParameter(2, $action);

        return $this->getEntityFromPersistence();
    }


    /**
     * Setting member to action ACL
     *
     * @param string $member
     * @param string $action
     * @param int $status
     * @return bool
     */
    public function setMemberAndActionAcl($member, $action, $status)
    {
        $acl = $this->getMemberAndActionAcl($member, $action);
        if (AclMember::STATUS_DEFAULT == $status) {
            if ($acl instanceof AclMember) {
                $this->removeEntity($acl);
            }
            return true;
        }

        if ($acl instanceof AclMember) {
            $acl->setStatus($status);
            $this->saveModifiedEntity($acl);
        } else {
            $acl = new AclMember();
            $acl->setMember($member);
            $acl->setAction($action);
            $acl->setStatus($status);
            $this->saveModifiedEntity($acl);
        }

        return true;
    }



    /**
     * Get all acl, max records: 200
     *
     * @param string $action
     * @return array
     */
    public function getDepartmentAndActionAllAclByAction($action)
    {
        $this->resetQb();

        $this->getQb()->select('t')->from(AclDepartment::class, 't');
        $this->getQb()->where($this->getQb()->expr()->eq('t.action', '?1'));
        $this->getQb()->setParameter(1, $action);

        $this->getQb()->setMaxResults(200)->setFirstResult(0);

        return $this->getEntitiesFromPersistence();
    }


    /**
     * Get all acl, max records: 200
     *
     * @param string $dept_id
     * @return array
     */
    public function getDepartmentAndActionAllAclByDepartment($dept_id)
    {
        $this->resetQb();

        $this->getQb()->select('t')->from(AclDepartment::class, 't');
        $this->getQb()->where($this->getQb()->expr()->eq('t.dept', '?1'));
        $this->getQb()->setParameter(1, $dept_id);

        $this->getQb()->setMaxResults(200)->setFirstResult(0);

        return $this->getEntitiesFromPersistence();
    }


    /**
     * Get acl by SQL IN()
     *
     * @param array $dept_ids
     * @return array
     */
    public function getDepartmentAndActionAllAclByDepartmentIds($dept_ids)
    {
        $this->resetQb();

        $this->getQb()->select('t')->from(AclDepartment::class, 't');
        $this->getQb()->where($this->getQb()->expr()->in('t.dept', $dept_ids));

        $this->getQb()->setMaxResults(200)->setFirstResult(0);

        return $this->getEntitiesFromPersistence();
    }



    /**
     * Get a record for department and action
     *
     * @param string $dept
     * @param string $action
     * @return AclDepartment
     */
    public function getDepartmentAndActionAcl($dept, $action)
    {
        $this->resetQb();

        $this->getQb()->select('t')->from(AclDepartment::class, 't');
        $this->getQb()->where(
            $this->getQb()->expr()->andX(
                $this->getQb()->expr()->eq('t.dept', '?1'),
                $this->getQb()->expr()->eq('t.action', '?2')
            )
        );
        $this->getQb()->setParameter(1, $dept)->setParameter(2, $action);

        return $this->getEntityFromPersistence();
    }


    /**
     * Setting department to action ACL
     *
     * @param string $dept
     * @param string $action
     * @param int $status
     * @return bool
     */
    public function setDepartmentAndActionAcl($dept, $action, $status)
    {
        $acl = $this->getDepartmentAndActionAcl($dept, $action);
        if (AclDepartment::STATUS_FORBIDDEN == $status) {
            if ($acl instanceof AclDepartment) {
                $this->removeEntity($acl);
            }
            return true;
        }

        if ($acl instanceof AclDepartment) {
            $acl->setStatus($status);
            $this->saveModifiedEntity($acl);
        } else {
            $acl = new AclDepartment();
            $acl->setDept($dept);
            $acl->setAction($action);
            $acl->setStatus($status);
            $this->saveModifiedEntity($acl);
        }

        return true;
    }


    /**
     * Remove a action all acl
     *
     * @param string $action
     */
    public function removeAction($action)
    {
        $rows = $this->getMemberAndActionAllAclByAction($action);
        if (!empty($rows)) {
            $this->removeEntities($rows);
        }

        $rows = $this->getDepartmentAndActionAllAclByAction($action);
        if (!empty($rows)) {
            $this->removeEntities($rows);
        }
    }


    /**
     * Get current member menus
     *
     * @return array
     */
    public function getMyMenus()
    {
        $member = $this->memberManager->getCurrentMember();
        if (null == $member) {
            return [];
        }

        $isSupperAdmin = false;
        $actionIds = [];
        if (Member::LEVEL_SUPERIOR == $member->getMemberLevel()) {
            $isSupperAdmin = true;
        } else {
            $actionIds = $this->getMemberMergedAcl($member);
        }

        if (!$isSupperAdmin && empty($actionIds)) {
            return [];
        }

        // Rebuild menu
        $menu = [];
        $rows = $this->componentManager->getComponentsForAutoMenu();
        foreach ($rows as $entity) {
            if ($entity instanceof Component) {
                $subMenus = [];
                $actions = $entity->getActions();
                foreach ($actions as $action) {
                    if ($action instanceof Action) {
                        if (Action::MENU_YES == $action->getActionMenu() &&
                            ($isSupperAdmin || in_array($action->getActionId(), $actionIds))
                        ) {
                            $rank = $action->getActionRank();
                            $subItem = [
                                'key' => $action->getActionKey(),
                                'name' => $action->getActionName(),
                                'icon' => $action->getActionIcon(),
                            ];
                            if (!array_key_exists($rank, $subMenus)) {
                                $subMenus[$rank] = $subItem;
                            } else {
                                $subMenus[($rank + rand(1111, 9999))] = $subItem;
                            }
                        }
                    }
                }

                if (!empty($subMenus)) {
                    $item = [
                        'class' => $entity->getComClass(),
                        'name' => $entity->getComName(),
                        'icon' => $entity->getComIcon(),
                        'route' => $entity->getComRoute(),
                    ];

                    krsort($subMenus);
                    $item['actions'] = $subMenus;

                    $menu[] = $item;
                }
            }
        }

        return $menu;
    }


    /**
     * @param Member $member
     * @return array
     */
    private function getMemberMergedAcl($member)
    {
        $forbiddenActionIds = [];
        $allowedActionIds = [];

        $rows = $this->getMemberAndActionAllAclByMember($member->getMemberId());
        foreach ($rows as $acl) {
            if ($acl instanceof AclMember) {
                if (AclMember::STATUS_FORBIDDEN == $acl->getStatus()) {
                    $forbiddenActionIds[$acl->getAction()] = $acl->getAction();
                }
                if (AclMember::STATUS_ALLOWED == $acl->getStatus()) {
                    $allowedActionIds[$acl->getAction()] = $acl->getAction();
                }
            }
        }

        // Member departments
        $departmentIds = [];
        $departments = $member->getDepts();
        foreach ($departments as $department) {
            if ($department instanceof Department) {
                if ($department->getDeptStatus() == Department::STATUS_VALID) {
                    $departmentIds[$department->getDeptId()] = $department->getDeptId();
                }
            }
        }

        // Department acl.
        $rows = $this->getDepartmentAndActionAllAclByDepartmentIds($departmentIds);
        foreach ($rows as $acl) {
            if ($acl instanceof AclDepartment) {
                $allowedActionIds[$acl->getAction()] = $acl->getAction();
            }
        }

        /**
        foreach ($departmentIds as $departmentId) {
            $rows = $this->getDepartmentAndActionAllAclByDepartment($departmentId);
            foreach ($rows as $acl) {
                if ($acl instanceof AclDepartment) {
                    $allowedActionIds[$acl->getAction()] = $acl->getAction();
                }
            }
        }
        //*/

        // Merged can access action ids
        $actionIds = [];
        foreach ($allowedActionIds as $id) {
            if (!in_array($id, $forbiddenActionIds)) {
                $actionIds[$id] = $id;
            }
        }
        return $actionIds;
    }


    /**
     * Access validate.
     *
     * @param string $controllerClass
     * @param string $actionKey
     * @return bool
     */
    public function isValid($controllerClass, $actionKey)
    {
        $member = $this->memberManager->getCurrentMember();

        if (null == $member || Member::STATUS_ACTIVATED != $member->getMemberStatus()) {
            return false;
        }

        if (Member::LEVEL_SUPERIOR == $member->getMemberLevel()) { // For supper administrator
            return true;
        }

        $allowed = $this->getMemberMergedAcl($member);
        if (empty($allowed)) {
            return false;
        }

        $component = $this->componentManager->getComponent($controllerClass);
        if (null == $component) {
            return false;
        }

        $actionId = null;
        $actions = $component->getActions();
        foreach ($actions as $action) {
            if ($action instanceof Action) {
                if ($actionKey == $action->getActionKey()) {
                    $actionId = $action->getActionId();
                    break;
                }
            }
        }

        if (empty($actionId)) {
            return false;
        }

        return in_array($actionId, $allowed);
    }


}