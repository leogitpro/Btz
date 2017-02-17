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
use Doctrine\ORM\EntityManager;
use Ramsey\Uuid\Uuid;
use Zend\Log\Logger;


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
     */
    public function getDepartment($dept_id)
    {
        $this->resetQb();

        $this->getQb()->from(Department::class, 't')->select('t');
        $this->getQb()->where($this->getQb()->expr()->eq('t.deptId', '?1'));
        $this->getQb()->setParameter(1, $dept_id);

        return $this->getEntityFromPersistence();
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

}