<?php
/**
 * DeptNameUniqueValidator.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Validator;


use Zend\Validator\AbstractValidator;

class DeptNameUniqueValidator extends AbstractValidator
{

    const DEPT_EXISTED = 'departmentExisted';

    protected $options = [
        'departmentManager' => null,
        'department' => null,
    ];

    protected $messageTemplates = [
        self::DEPT_EXISTED => 'The department name already exists.',
    ];


    /**
     * DeptNameUniqueValidator constructor.
     *
     * @param array $options
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            if (isset($options['departmentManager'])) {
                $this->options['departmentManager'] = $options['departmentManager'];
            }
            if (isset($options['department'])) {
                $this->options['department'] = $options['department'];
            }
        }

        parent::__construct($options);
    }


    /**
     * Check the department name is unique.
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {

        $deptManager = $this->options['departmentManager'];
        $dept = $this->options['department'];

        $existedDept = $deptManager->getDepartmentByName($value);

        if (null == $dept) { // Created

            if (null == $existedDept) {
                return true;
            } else {
                $this->error(self::DEPT_EXISTED);
                return false;
            }
        } else {
            $name = $dept->getDeptName();
            if ($value == $name) { // No modified
                return true;
            } else {
                if (null == $existedDept) { // modified to new name. unique.
                    return true;
                } else { // The new name is existed.
                    $this->error(self::DEPT_EXISTED);
                    return false;
                }
            }
        }
    }


}