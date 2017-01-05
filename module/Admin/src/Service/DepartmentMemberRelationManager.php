<?php
/**
 * Department and member relationship manager
 *
 * User: leo
 */

namespace Admin\Service;


use Admin\Entity\DepartmentMember;

class DepartmentMemberRelationManager extends BaseEntityManager
{


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
     * Get department members id
     *
     * @param $dept_id
     * @return array
     */
    public function getDepartmentMemberIds($dept_id)
    {
        $rows = $this->getDepartmentMemberRelations($dept_id);
        if (empty($rows)) {
            return [];
        }
        $ids = [];
        foreach ($rows as $row) {
            if ($row instanceof DepartmentMember) {
                $ids[$row->getMemberId()] = $row->getMemberId();
            }
        }
        return $ids;
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
     * Get member departments id
     *
     * @param $member_id
     * @return array
     */
    public function getMemberDepartmentIds($member_id)
    {
        $rows = $this->getMemberDepartmentRelations($member_id);
        if (empty($rows)) {
            return [];
        }

        $ids = [];
        foreach($rows as $row) {
            if ($row instanceof DepartmentMember) {
                $ids[$row->getDeptId()] = $row->getDeptId();
            }
        }

        return $ids;
    }


}