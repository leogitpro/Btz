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
use Zend\Log\Logger;


class DepartmentManager extends BaseEntityManager
{

    /**
     * @var DMRelationManager
     */
    private $dmrManager;


    public function __construct(DMRelationManager $dmrManager, EntityManager $entityManager, Logger $logger)
    {
        parent::__construct($entityManager, $logger);

        $this->dmrManager = $dmrManager;
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
     * Get department information
     *
     * @param integer $dept_id
     * @return Department
     */
    public function getDepartment($dept_id)
    {
        return $this->getUniverseDepartment(['dept_id' => $dept_id]);
    }


    /**
     * Get department information by name.
     *
     * @param string $name
     * @return Department
     */
    public function getDepartmentByName($name)
    {
        return $this->getUniverseDepartment(['dept_name' => $name]);
    }


    /**
     * Get a department
     *
     * @param array $criteria
     * @param null|array $order
     * @return Department
     */
    private function getUniverseDepartment($criteria = [], $order = null)
    {
        return $this->entityManager->getRepository(Department::class)->findOneBy($criteria, $order);
    }



    /**
     * Get all departments count
     *
     * @return integer
     */
    public function getDepartmentsCount()
    {
        return $this->getEntitiesCount(
            Department::class,
            'dept_id',
            ['dept_status = :status'],
            ['status' => Department::STATUS_VALID]
        );
    }


    /**
     * Get departments by page and offset
     *
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getDepartmentsByLimitPage($page = 1, $size = 10)
    {
        return $this->getUniverseDepartments([
            'dept_status' => Department::STATUS_VALID,
        ], null, $size, ($page - 1) * $size);
    }


    /**
     * Get all valid departments, Maxsize: 200
     *
     * @return array
     */
    public function getDepartments()
    {
        return $this->getUniverseDepartments([
            'dept_status' => Department::STATUS_VALID,
        ], null, 200);
    }


    /**
     * Get all departments count
     *
     * @return integer
     */
    public function getAllDepartmentsCount()
    {
        return $this->getEntitiesCount(Department::class, 'dept_id');
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
        return $this->getUniverseDepartments([], null, $size, ($page - 1) * $size);
    }


    /**
     * Get all departments, Maxsize: 200
     *
     * @return array
     */
    public function getAllDepartments()
    {
        return $this->getUniverseDepartments([], null, 200);
    }


    /**
     * Get departments
     *
     * @param array $criteria
     * @param null|array $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    private function getUniverseDepartments($criteria = [], $order = null, $limit = 10, $offset = 0)
    {
        if (null == $order) {
            $order = [
                'dept_status' => 'DESC',
                'dept_name' => 'ASC',
            ];
        }
        return $this->entityManager->getRepository(Department::class)->findBy($criteria, $order, $limit, $offset);
    }


    /**
     * Get the department all member ids
     *
     * @param $dept_id
     * @return array
     */
    public function getDepartmentAllMemberIds($dept_id)
    {
        $rows = $this->dmrManager->departmentRelations($dept_id);
        $ids = [];
        foreach ($rows as $entity) {
            if ($entity instanceof DepartmentMember) {
                $ids[$entity->getMemberId()] = $entity->getMemberId();
            }
        }
        return $ids;
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
     * Update department status
     *
     * @param Department $dept
     * @param int $status
     * @return void
     */
    public function updateDepartmentStatus(Department $dept, $status)
    {
        $oldStatus = $dept->getDeptStatus();
        if ($oldStatus == $status) {
            return false;
        }

        if ($oldStatus == Department::STATUS_VALID) { // to be invalid

            $this->dmrManager->departmentToBeInvalid($dept->getDeptId()); // Remove all members relationship

            $dept->setDeptStatus(Department::STATUS_INVALID);
            $dept->setDeptMembers(0);
            $this->saveModifiedEntity($dept);

        } else { // to be valid

            $dept->setDeptStatus(Department::STATUS_VALID);
            $this->saveModifiedEntity($dept);

            $this->dmrManager->departmentBeActivated($dept->getDeptId());

        }

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
        $dept->setDeptStatus(Department::STATUS_VALID);
        $dept->setDeptCreated(new \DateTime());

        return $this->saveModifiedEntity($dept);
    }


}