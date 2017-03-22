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
    private function addNameElement()
    {
        $value = '';
        if($this->dept instanceof Department) {
            $value = $this->dept->getDeptName();
        }

        $this->addElement([
            'type' => 'text',
            'name' => 'name',
            'attributes' => [
                'id' => 'name',
                'value' => $value,
            ],
            'options' => [
                'label' => '部门名称',
            ],
        ]);

        $this->addFilter([
            'name' => 'name',
            'required' => true,
            'break_on_failure' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
            ],
            'validators' => [
                [
                    'name' => 'StringLength',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'min' => 2,
                        'max' => 45,
                    ],
                ],
                [
                    'name' => DeptNameUniqueValidator::class,
                    'break_chain_on_failure' => true,
                    'options' => [
                        'departmentManager' => $this->deptManager,
                        'department' => $this->dept,
                    ],
                ],
            ],
        ]);
    }


    public function addElements()
    {
        $this->addNameElement();
    }
}