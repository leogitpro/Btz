<?php
/**
 * DepartmentManager.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Service;


use Admin\Entity\Department;
use Doctrine\ORM\EntityManager;
use Zend\Log\Logger;

class DepartmentManager
{

    /**
     * @var EntityManager
     */
    private $entityManager;


    /**
     * @var Logger
     */
    private $logger;



    public function __construct(EntityManager $entityManager, Logger $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
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
        $this->entityManager->persist($dept);
        $this->entityManager->flush();

        return $dept;
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
        $dept->setDeptStatus(Department::STATUS_VALID);
        $dept->setDeptCreated(date('Y-m-d H:i:s'));

        $this->entityManager->persist($dept);
        $this->entityManager->flush();

        return $dept;
    }


}