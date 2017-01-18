<?php
/**
 * DMRelationManager.php
 *
 * The department with member relation manager
 *
 * @author: leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Service;


use Admin\Entity\Department;
use Admin\Entity\DepartmentMember;
use Admin\Entity\Member;

class DMRelationManager extends BaseEntityManager
{

    /**
     * Get the department valid members count
     *
     * @param int $deptId
     * @return int
     */
    public function getDepartmentMembersCount($deptId)
    {
        return $this->getEntitiesCount(
            DepartmentMember::class,
            'id',
            ['dept_id = :dept_id AND t.status = :status'],
            ['dept_id' => $deptId, 'status' => DepartmentMember::STATUS_VALID],
            't'
        );

    }

    /**
     * Get a relationship
     *
     * @param int $memberId
     * @param int $deptId
     * @return DepartmentMember
     */
    public function getRelation($memberId, $deptId)
    {
        return $this->getUniverseRelation([
            'member_id' => $memberId,
            'dept_id' => $deptId,
        ]);
    }


    /**
     * Get a relation
     *
     * @param array $criteria
     * @param null $order
     * @return DepartmentMember
     */
    public function getUniverseRelation($criteria, $order = null)
    {
        return $this->entityManager->getRepository(DepartmentMember::class)->findOneBy($criteria, $order);
    }


    /**
     * Get a member all valid relations. maxsize: 200
     *
     * @param int $memberId
     * @return array
     */
    public function memberRelations($memberId)
    {
        return $this->getUniverseRelations([
            'member_id' => $memberId,
            'status' => DepartmentMember::STATUS_VALID,
        ], null, 200);
    }


    /**
     * Get a department all valid relations. maxsize: 200
     *
     * @param int $deptId
     * @return array
     */
    public function departmentRelations($deptId)
    {
        return $this->getUniverseRelations([
            'dept_id' => $deptId,
            'status' => DepartmentMember::STATUS_VALID,
        ], null, 200);
    }


    /**
     * Get a member all relations. maxsize: 200
     *
     * @param int $memberId
     * @return array
     */
    public function memberAllRelations($memberId)
    {
        return $this->getUniverseRelations(['member_id' => $memberId], null, 200);
    }


    /**
     * Get all relations
     *
     * @param array $criteria
     * @param null $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getUniverseRelations($criteria, $order = null, $limit = 10, $offset = 0)
    {
        return $this->entityManager->getRepository(DepartmentMember::class)->findBy($criteria, $order, $limit, $offset);
    }


    /**
     * Rebuild the department with members relationship
     *
     * @param int $deptId
     * @param array $memberIds
     * @return void
     */
    public function rebuildDepartmentMembers($deptId, $memberIds)
    {
        // Remove all existed members
        $entities = $this->departmentRelations($deptId);
        $i = 0;
        foreach ($entities as $entity) {
            if ($entity instanceof DepartmentMember) {
                $entity->setStatus(DepartmentMember::STATUS_INVALID);
                $this->entityManager->persist($entity);
                $i++;
                if (20 == $i) {
                    $this->entityManager->flush();
                    $i = 0;
                }
            }
        }
        if ($i) {
            $this->entityManager->flush();
        }


        if (empty($memberIds)) { // removed all members
            $this->syncDepartmentMembers($deptId);
            return ;
        }

        $i = 0;
        foreach ($memberIds as $memberId) {

            if (Member::DEFAULT_MEMBER_ID == $memberId) {
                continue;
            }

            $entity = $this->getRelation($memberId, $deptId);
            if ($entity instanceof DepartmentMember) {
                $entity->setStatus(DepartmentMember::STATUS_VALID);
            } else {
                $entity = new DepartmentMember();
                $entity->setDeptId($deptId);
                $entity->setMemberId($memberId);
                $entity->setStatus(DepartmentMember::STATUS_VALID);
                $entity->setCreated(new \DateTime());
            }

            $this->entityManager->persist($entity);
            $i++;
            if (20 == $i) {
                $this->entityManager->flush();
                $i = 0;
            }
        }
        if ($i) {
            $this->entityManager->flush();
        }

        $this->syncDepartmentMembers($deptId);
    }


    /**
     * Rebuild the member with departments relationship
     *
     * @param int $memberId
     * @param array $deptIds
     * @return void
     */
    public function rebuildMemberDepartments($memberId, $deptIds)
    {
        // Clean member all existed relationship
        $entities = $this->memberRelations($memberId);
        $i = 0;
        $oldDeptIds = [];
        foreach ($entities as $entity) {
            if ($entity instanceof DepartmentMember) {

                if (Department::DEFAULT_DEPT_ID == $entity->getDeptId()) {
                    continue;
                }

                $oldDeptIds[$entity->getDeptId()] = $entity->getDeptId();

                $entity->setStatus(DepartmentMember::STATUS_INVALID);
                $this->entityManager->persist($entity);

                $i++;
                if (20 == $i) {
                    $this->entityManager->flush();
                    $i = 0;
                }
            }
        }
        if ($i) {
            $this->entityManager->flush();
        }

        if (empty($deptIds)) {
            foreach ($oldDeptIds as $deptId) {
                $this->syncDepartmentMembers($deptId);
            }
            return ;
        }

        $i = 0;
        foreach ($deptIds as $deptId) {

            if (Department::DEFAULT_DEPT_ID == $deptId) {
                continue;
            }

            $oldDeptIds[$deptId] = $deptId;

            $entity = $this->getRelation($memberId, $deptId);
            if ($entity instanceof DepartmentMember) {
                $entity->setStatus(DepartmentMember::STATUS_VALID);
            } else {
                $entity = new DepartmentMember();
                $entity->setDeptId($deptId);
                $entity->setMemberId($memberId);
                $entity->setStatus(DepartmentMember::STATUS_VALID);
                $entity->setCreated(new \DateTime());
            }
            $this->entityManager->persist($entity);
            $i++;
            if (20 == $i) {
                $this->entityManager->flush();
                $i = 0;
            }
        }

        if ($i) {
            $this->entityManager->flush();
        }

        foreach ($oldDeptIds as $deptId) {
            $this->syncDepartmentMembers($deptId);
        }
    }


    /**
     * @param int $deptId
     */
    public function departmentToBeInvalid($deptId)
    {
        // Close the department owned members
        $entities = $this->departmentRelations($deptId);
        $i = 0;
        foreach ($entities as $entity) {
            if ($entity instanceof DepartmentMember) {
                $entity->setStatus(DepartmentMember::STATUS_INVALID);
                $this->entityManager->persist($entity);
                $i++;
                if (20 == $i) {
                    $this->entityManager->flush();
                    $i = 0;
                }
            }
        }
        if ($i) {
            $this->entityManager->flush();
        }
    }


    /**
     * @param int $deptId
     */
    public function departmentBeActivated($deptId)
    {
        //activated a department restore some status ...
    }


    /**
     * Close a member account. clean the member relation
     *
     * @param $memberId
     */
    public function memberToBeInvalid($memberId)
    {
        // Close the member joined departments
        $entities = $this->memberRelations($memberId);
        $i = 0;
        $deptIds = [];
        foreach ($entities as $entity) {
            if ($entity instanceof DepartmentMember) {

                $deptIds[$entity->getDeptId()] = $entity->getDeptId();

                $entity->setStatus(DepartmentMember::STATUS_INVALID);
                $this->entityManager->persist($entity);

                $i++;
                if (20 == $i) {
                    $this->entityManager->flush();
                    $i = 0;
                }
            }
        }
        if ($i) {
            $this->entityManager->flush();
        }

        // Sync the relation department members count.
        // Bad design for static members count. Nice for actual application
        foreach ($deptIds as $deptId) {
            $this->syncDepartmentMembers($deptId);
        }

    }

    /**
     * Sync department members count
     *
     * @param int $deptId
     */
    private function syncDepartmentMembers($deptId)
    {
        $count = $this->getDepartmentMembersCount($deptId);
        $dept = $this->entityManager->getRepository(Department::class)->find($deptId);
        if ($dept instanceof Department) {
            $dept->setDeptMembers($count);
            $this->entityManager->persist($dept);
            $this->entityManager->flush();
        }
    }


    /**
     * Active a member. restore member to default department relationship
     *
     * @param $memberId
     */
    public function memberBeActivated($memberId)
    {
        // Restore member with default department
        $relation = $this->getRelation($memberId, Department::DEFAULT_DEPT_ID);
        if (null == $relation) {
            $relation = new DepartmentMember();
            $relation->setMemberId($memberId);
            $relation->setDeptId(Department::DEFAULT_DEPT_ID);
            $relation->setStatus(DepartmentMember::STATUS_INVALID);
            $relation->setCreated(new \DateTime());
            $relation = $this->saveModifiedEntity($relation);
        }

        $relation->setStatus(DepartmentMember::STATUS_VALID);
        $this->saveModifiedEntity($relation);

        $this->syncDepartmentMembers(Department::DEFAULT_DEPT_ID);
    }


    /**
     * Init a new member created with relation relationships
     *
     * @param int $memberId
     */
    public function initNewMemberCreated($memberId)
    {
        $relation = new DepartmentMember();
        $relation->setMemberId($memberId);
        $relation->setDeptId(Department::DEFAULT_DEPT_ID);
        $relation->setStatus(DepartmentMember::STATUS_INVALID);
        $relation->setCreated(new \DateTime());

        $this->saveModifiedEntity($relation);
    }

}