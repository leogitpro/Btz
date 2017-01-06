<?php
/**
 * DepartmentManager.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Service;


use Admin\Entity\Department;
use Admin\Entity\DepartmentMember;

class DepartmentManager extends BaseEntityManager
{

    /**
     * Get all departments count
     *
     * @return integer
     */
    public function getAllDepartmentsCount()
    {
        $qb = $this->entityManager->getRepository(Department::class)->createQueryBuilder('t');
        return $qb->select('count(t.dept_id)')->getQuery()->getSingleScalarResult();
    }


    /**
     * Get departments by page and offset
     *
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getAllDepartmentByLimitPage($page = 1, $size = 10)
    {
        return $this->entityManager->getRepository(Department::class)->findBy([], ['dept_id' => 'ASC',], $size, ($page - 1) * $size);
    }


    /**
     * Get all departments
     *
     * @return array
     */
    public function getAllDepartments()
    {
        return $this->entityManager->getRepository(Department::class)->findAll();
    }

    /**
     * Get all valid departments
     *
     * @return array
     */
    public function getDepartments()
    {
        return $this->entityManager->getRepository(Department::class)->findBy([
            'dept_status' => Department::STATUS_VALID,
        ]);
    }


    /**
     * Get department information
     *
     * @param integer $dept_id
     * @return Department
     */
    public function getDepartment($dept_id)
    {
        return $this->entityManager->getRepository(Department::class)->find($dept_id);
    }


    /**
     * Get default department
     *
     * @return Department
     */
    public function getDefaultDepartment()
    {
        return $this->getDepartment(Department::DEFAULT_DEPT_ID);
    }


    /**
     * Get department information by name.
     *
     * @param string $name
     * @return Department
     */
    public function getDepartmentByName($name)
    {
        return $this->entityManager->getRepository(Department::class)->findOneBy(['dept_name' => $name]);
    }


    /**
     * Save modified department data
     *
     * @param Department $dept
     * @return Department
     */
    public function saveModifiedDepartment(Department $dept)
    {
        return $this->saveModifiedEntity($dept);
    }


    /**
     * Update a department members count
     *
     * @param integer $dept_id
     * @param integer $members_count
     * @return Department
     */
    public function updateDepartmentMembersCount($dept_id, $members_count)
    {
        $department = $this->getDepartment($dept_id);
        if (null != $department) {
            $department->setDeptMembers($members_count);
            return $this->saveModifiedEntity($department);
        }
        return $department;
    }


    /**
     * Update department status
     *
     * @param Department $dept
     * @param int $status
     * @return Department
     */
    public function updateDepartmentStatus(Department $dept, $status)
    {
        $oldStatus = $dept->getDeptStatus();
        if ($oldStatus == $status) {
            return false;
        }

        if ($oldStatus == Department::STATUS_VALID) { // to be invalid
            // Clean all relationship
            $rows = $this->entityManager->getRepository(DepartmentMember::class)->findBy([
                'dept_id' => $dept->getDeptId(),
                'status' => DepartmentMember::STATUS_VALID,
            ]);
            foreach ($rows as $row) {
                if ($row instanceof DepartmentMember) {
                    $row->setStatus(DepartmentMember::STATUS_INVALID);
                    $this->entityManager->persist($row);
                }
            }
            $this->entityManager->flush();

            $dept->setDeptStatus(Department::STATUS_INVALID);
        } else { // to be valid
            $dept->setDeptStatus(Department::STATUS_VALID);
        }

        $dept->setDeptMembers(0);
        return $this->saveModifiedEntity($dept);
    }


    /**
     * Create an department information.
     *
     * @param string $name
     * @return Department
     */
    public function createDepartment($name)
    {
        $dept = new Department();
        $dept->setDeptName($name);
        $dept->setDeptMembers(0);
        //$dept->setDeptStatus(Department::STATUS_VALID);
        $dept->setDeptCreated(new \DateTime());

        $this->entityManager->persist($dept);
        $this->entityManager->flush();

        return $dept;
    }


}