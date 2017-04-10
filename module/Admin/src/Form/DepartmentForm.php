<?php
/**
 * DepartmentForm.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Form;


use Admin\Entity\Department;
use Admin\Service\DepartmentManager;
use Admin\Validator\DeptNameUniqueValidator;
use Form\Form\BaseForm;
use Form\Validator\Factory;


class DepartmentForm extends BaseForm
{

    /**
     * @var Department
     */
    private $dept;

    /**
     * @var DepartmentManager
     */
    private $deptManager;


    public function __construct(DepartmentManager $departmentManager, $dept = null)
    {

        $this->deptManager = $departmentManager;
        $this->dept = $dept;

        parent::__construct();
    }


    /**
     * 表单: 部门名称
     */
    private function addDepartmentName()
    {
        $validators = [
            Factory::StringLength(2, 45),
            [
                'name' => DeptNameUniqueValidator::class,
                'break_chain_on_failure' => true,
                'options' => [
                    'departmentManager' => $this->deptManager,
                    'department' => $this->dept,
                ],
            ],
        ];

        $this->addTextElement('name', true, $validators);
    }


    public function addElements()
    {
        $this->addDepartmentName();
    }
}