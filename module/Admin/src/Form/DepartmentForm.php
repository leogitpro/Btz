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
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class DepartmentForm extends Form
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
        parent::__construct('dept_form');

        $this->deptManager = $departmentManager;
        $this->dept = $dept;

        $this->setAttributes(['method' => 'post', 'role' => 'form']);

        $this->setInputFilter(new InputFilter());

        $this->addElements();
        $this->addFilters();
    }


    public function addElements()
    {
        $this->add([
            'type'  => 'csrf',
            'name' => 'csrf',
            'attributes' => [],
            'options' => [
                'csrf_options' => [
                    'timeout' => 600
                ]
            ],
        ]);

        $this->add([
            'type' => 'text',
            'name' => 'name',
            'attributes' => [
                'id' => 'name',
                'value' => (null == $this->dept) ? '' : $this->dept->getDeptName(),
            ],
            'options' => [
                'label' => 'Department Name',
            ],
        ]);

        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'id' => 'submit',
                'value' => 'Submit',
            ],
        ]);
    }


    public function addFilters()
    {
        $this->getInputFilter()->add([
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

}