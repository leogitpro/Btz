<?php
/**
 * DeptIdValidator.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Validator;


use Admin\Entity\Department;
use Admin\Service\DepartmentManager;
use Zend\Validator\AbstractValidator;

class DeptIdValidator extends AbstractValidator
{

    const ID_INVALID = 'deptIdInvalid';

    protected $options = [
        'deptManager' => null
    ];

    protected $messageTemplates = [
        self::ID_INVALID => 'The department is invalid',
    ];


    public function __construct($options = null)
    {
        if (is_array($options)) {
            if (isset($options['deptManager'])) {
                $this->options['deptManager'] = $options['deptManager'];
            }
        }

        parent::__construct($options);
    }


    /**
     * Check the member id is existed
     *
     * @param string $value
     * @return bool
     */
    public function isValid($value)
    {
        if (empty($value)) {
            $this->error(self::ID_INVALID);
            return false;
        }

        $deptManager = $this->options['deptManager'];
        if (!($deptManager instanceof DepartmentManager)) {
            $this->error(self::ID_INVALID);
            return false;
        }

        $dept = $deptManager->getDepartment($value);
        if (!($dept instanceof Department) || $dept->getDeptStatus() != Department::STATUS_VALID) {
            $this->error(self::ID_INVALID);
            return false;
        }

        return true;
    }



}