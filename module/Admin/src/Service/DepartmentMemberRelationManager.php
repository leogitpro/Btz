<?php
/**
 * Department and member relationship manager
 *
 * User: leo
 */

namespace Admin\Service;


use Admin\Entity\Department;
use Admin\Entity\DepartmentMember;
use Doctrine\ORM\EntityManager;
use Zend\Log\Logger;

class DepartmentMemberRelationManager extends BaseEntityManager
{
    /**
     * @var DepartmentManager
     */
    private $departmentManager;


    public function __construct(
        EntityManager $entityManager,
        Logger $logger,
        DepartmentManager $departmentManager
    )
    {
        $this->departmentManager = $departmentManager;

        parent::__construct($entityManager, $logger);
    }


    /**
     * Get the relationship
     *
     * @param integer $member_id
     * @param integer $dept_id
     * @return DepartmentMember
     */
    public function getRelationship($member_id, $dept_id)
    {
        return $this->entityManager->getRepository(DepartmentMember::class)->findOneBy([
            'member_id' => $member_id,
            'dept_id' => $dept_id,
        ]);
    }


    /**
     * Remove member with department relationship
     * close the status
     * re-count the department members count
     *
     * @param integer $member_id
     * @param integer $dept_id
     * @return bool
     */
    public function removeMemberFromDepartment($member_id, $dept_id)
    {
        $relationship = $this->getRelationship($member_id, $dept_id);
        if (null == $relationship) {
            return false;
        }

        if ($relationship->getStatus() == DepartmentMember::STATUS_INVALID) {
            return false;
        }

        $relationship->setStatus(DepartmentMember::STATUS_INVALID);
        $this->saveModifiedEntity($relationship);

        $this->syncDepartmentMembersCount($dept_id);

        return true;
    }


    /**
     * Increase a member to department
     * build the relationship
     * re-count the department members count
     *
     * @param integer $member_id
     * @param integer $dept_id
     * @return bool
     */
    public function increaseMemberToDepartment($member_id, $dept_id)
    {
        $relationship = $this->getRelationship($member_id, $dept_id);
        if (null == $relationship) {
            $this->createRelationship($member_id, $dept_id, DepartmentMember::STATUS_VALID);
        } else {
            if ($relationship->getStatus() == DepartmentMember::STATUS_VALID) {
                return false;
            }

            $relationship->setStatus(DepartmentMember::STATUS_VALID);
            $this->saveModifiedEntity($relationship);
        }

        $this->syncDepartmentMembersCount($dept_id);

        return true;
    }


    /**
     * Get a department members count
     *
     * @param integer $dept_id
     * @return integer
     */
    public function getDepartmentMembersCount($dept_id)
    {
        $qb = $this->entityManager->getRepository(DepartmentMember::class)->createQueryBuilder('t');
        return $qb->select('count(t.id)')
            ->where('t.dept_id = :dept_id')
            ->andWhere('t.status = :status')
            ->setParameters(['dept_id' => $dept_id, 'status' => DepartmentMember::STATUS_VALID])
            ->getQuery()
            ->getSingleScalarResult();
    }


    /**
     * Get department members
     *
     * @param $dept_id
     * @return array
     */
    public function getDepartmentMemberRelations($dept_id)
    {
        return $this->entityManager->getRepository(DepartmentMember::class)->findBy([
            'dept_id' => $dept_id,
            'status' => DepartmentMember::STATUS_VALID,
        ]);
    }


    /**
     * Close a department
     *
     * @param integer $dept_id
     */
    public function closedOneDepartment($dept_id)
    {
        // Close all relationships with the department
        $entities = $this->getDepartmentMemberRelations($dept_id);
        foreach ($entities as $entity) {
            if ($entity instanceof DepartmentMember) {
                $entity->setStatus(DepartmentMember::STATUS_INVALID);
                $this->saveModifiedEntity($entity);
            }
        }

        // close other ...
    }


    /**
     * Opened a department
     *
     * @param integer $dept_id
     */
    public function openedOneDepartment($dept_id)
    {
        //todo
    }



    /**
     * Get member departments
     *
     * @param $member_id
     * @return array
     */
    public function getMemberDepartmentRelations($member_id)
    {
        return $this->entityManager->getRepository(DepartmentMember::class)->findBy([
            'member_id' => $member_id,
            'status' => DepartmentMember::STATUS_VALID,
        ]);
    }


    /**
     * Closed a member account
     *
     * @param integer $member_id
     */
    public function closedOneMember($member_id)
    {
        // Close member with all departments relationship
        $entities = $this->getMemberDepartmentRelations($member_id);
        foreach ($entities as $entity) {
            if ($entity instanceof DepartmentMember) {
                $this->removeMemberFromDepartment($member_id, $entity->getDeptId());
            }
        }

        // Close other ...
    }


    /**
     * Opened a member account
     *
     * @param integer $member_id
     */
    public function openedOneMember($member_id)
    {
        // Restore member with default department relationship
        $this->increaseMemberToDepartment($member_id, Department::DEFAULT_DEPT_ID);

        // Restore other ...
    }


    /**
     * sync the department members count
     *
     * @param integer $dept_id
     */
    public function syncDepartmentMembersCount($dept_id)
    {
        $membersCount = $this->getDepartmentMembersCount($dept_id);
        $this->departmentManager->updateDepartmentMembersCount($dept_id, $membersCount);
    }


    /**
     * Create a relationship
     *
     * @param integer $member_id
     * @param integer $dept_id
     * @param integer $status
     * @return DepartmentMember
     */
    public function createRelationship($member_id, $dept_id, $status)
    {
        $relationship = new DepartmentMember();
        $relationship->setMemberId($member_id);
        $relationship->setDeptId($dept_id);
        $relationship->setStatus($status);
        $relationship->setCreated(new \DateTime());

        $this->entityManager->persist($relationship);
        $this->entityManager->flush();

        return $relationship;
    }


    /**
     * Init new member departments relationship
     *
     * @param integer $member_id
     */
    public function initNewMemberDepartments($member_id)
    {
        $this->createRelationship($member_id, Department::DEFAULT_DEPT_ID, DepartmentMember::STATUS_INVALID);
    }


}