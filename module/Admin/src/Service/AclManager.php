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
     * @param int $member_id
     * @param string $controller_class
     * @param string $action_key
     * @return bool
     */
    public function isValid($member_id, $controller_class, $action_key)
    {
        $member = $this->memberManager->getMember($member_id);
        if (null == $member || Member::STATUS_ACTIVATED != $member->getMemberStatus()) {
            return false;
        }

        if (Member::LEVEL_SUPERIOR == $member->getMemberLevel()) { // For supper administrator
            return true;
        }

        $component = $this->componentManager->getComponentByClass($controller_class);
        if (null == $component || Component::STATUS_VALIDITY != $component->getComStatus()) {
            return false;
        }

        $action = $this->componentManager->getComponentAction($controller_class, $action_key);
        if (Action::STATUS_VALIDITY != $action->getActionStatus()) {
            return false;
        }

        $actionId = $action->getActionId();
        $memberId = $member->getMemberId();

        $acl = $this->getMemberActionAcl($memberId, $actionId);
        if ($acl instanceof AclMember) {
            $status = $acl->getStatus();
            if ($status == AclMember::STATUS_ALLOWED) {
                return true;
            }
            if ($status == AclMember::STATUS_FORBIDDEN) {
                return false;
            }
        }

        $access = false;

        $relations = $this->dmrManager->memberRelations($memberId);
        foreach ($relations as $relation) {
            if ($relation instanceof DepartmentMember) {
                if (DepartmentMember::STATUS_VALID != $relation->getStatus()) {
                    continue;
                }

                $deptId = $relation->getDeptId();
                $acl = $this->getDepartmentActionAcl($deptId, $actionId);
                if ($acl instanceof AclDepartment) {
                    if (AclDepartment::STATUS_ALLOWED == $acl->getStatus()) {
                        $access = true;
                        break;
                    }
                }

            }
        }

        return $access;
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