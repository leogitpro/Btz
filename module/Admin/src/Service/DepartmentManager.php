<?php
/**
 * DepartmentManager.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Service;


use Admin\Entity\Department;
use Admin\Exception\InvalidArgumentException;


class DepartmentManager extends BaseEntityManager
{

    /**
     * Get departments count
     *
     * @return integer
     */
    public function getDepartmentsCount()
    {
        $this->resetQb();

        $this->getQb()->select($this->getQb()->expr()->count('t.deptId'));
        $this->getQb()->from(Department::class, 't');

        $this->getQb()->where($this->getQb()->expr()->eq('t.deptStatus', '?1'));
        $this->getQb()->setParameter(1, Department::STATUS_VALID);

        return $this->getEntitiesCount();
    }


    /**
     * Get all departments count
     *
     * @return integer
     */
    public function getAllDepartmentsCount()
    {
        $this->resetQb();

        $this->getQb()->select($this->getQb()->expr()->count('t.deptId'));
        $this->getQb()->from(Department::class, 't');

        return $this->getEntitiesCount();
    }


    /**
     * Get department information
     *
     * @param string $dept_id
     * @return Department
     * @throws InvalidArgumentException
     */
    public function getDepartment($dept_id = null)
    {
        if (empty($dept_id)) {
            throw new InvalidArgumentException('不能查询空的编号');
        }

        $qb = $this->resetQb();

        $qb->from(Department::class, 't')->select('t');
        $qb->where($qb->expr()->eq('t.deptId', '?1'));
        $qb->setParameter(1, $dept_id);

        $obj = $this->getEntityFromPersistence();
        if (!$obj instanceof Department) {
            throw new InvalidArgumentException('无效的编号');
        }
        return $obj;
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
     * Get weChat department
     *
     * @return Department
     */
    public function getWeChatDepartment()
    {
        return $this->getDepartment(Department::WE_CHAT_DEPT_ID);
    }


    /**
     * Get department information by name.
     *
     * @param string $name
     * @return Department
     */
    public function getDepartmentByName($name)
    {
        $this->resetQb();

        $this->getQb()->from(Department::class, 't')->select('t');
        $this->getQb()->where($this->getQb()->expr()->eq('t.deptName', '?1'));
        $this->getQb()->setParameter(1, $name);

        return $this->getEntityFromPersistence();
    }


    /**
     * Get departments by page and offset
     *
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getDepartmentsByLimitPage($page = 1, $size = 100)
    {
        $this->resetQb();

        $this->getQb()->select('t')->from(Department::class, 't');

        $this->getQb()->where($this->getQb()->expr()->eq('t.deptStatus', '?1'));
        $this->getQb()->setParameter(1, Department::STATUS_VALID);

        $this->getQb()->setMaxResults($size)->setFirstResult(($page -1) * $size);

        $this->getQb()->orderBy('t.deptStatus', 'DESC')->addOrderBy('t.deptName');

        return $this->getEntitiesFromPersistence();
    }


    /**
     * Get all valid departments, Maxsize: 200
     *
     * @return array
     */
    public function getDepartments()
    {
        return $this->getDepartmentsByLimitPage(1, 200);
    }


    /**
     * Get departments by page and offset
     *
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getAllDepartmentsByLimitPage($page = 1, $size = 10)
    {
        $this->resetQb();

        $this->getQb()->select('t')->from(Department::class, 't');

        $this->getQb()->setMaxResults($size)->setFirstResult(($page -1) * $size);

        $this->getQb()->orderBy('t.deptStatus', 'DESC')->addOrderBy('t.deptName');

        return $this->getEntitiesFromPersistence();
    }


    /**
     * Get all departments, Maxsize: 200
     *
     * @return array
     */
    public function getAllDepartments()
    {
        return $this->getAllDepartmentsByLimitPage(1, 200);
    }


    /**
     * Search departments
     *
     * @param string $key
     * @return array
     */
    public function getDeptsBySearch($key = null)
    {
        if (empty($key)) {
            return [];
        }

        $qb = $this->resetQb();
        $qb->select('t')->from(Department::class, 't');

        $qb->where(
            $qb->expr()->andX(
                $qb->expr()->eq('t.deptStatus', '?1'),
                $qb->expr()->like('t.deptName', '?2')
            )
        );
        $qb->setParameter(1, Department::STATUS_VALID);
        $qb->setParameter(2, '%' . $key . '%');

        $qb->setMaxResults(10)->setFirstResult(0);

        $qb->orderBy('t.deptStatus', 'DESC')->addOrderBy('t.deptName');

        return $this->getEntitiesFromPersistence();
    }


}