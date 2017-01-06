<?php
/**
 * Department.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * Class Department
 * @package Admin\Entity
 * @ORM\Entity()
 * @ORM\Table(name="sys_department")
 */
class Department
{

    const STATUS_VALID = 1;
    const STATUS_INVALID = 0;

    const DEFAULT_DEPT_ID = 1;

    /**
     * Primary key, auto increment
     *
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="dept_id", type="integer")
     */
    private $dept_id;

    /**
     * @var string
     * @ORM\Column(name="dept_name", type="string", length=45, unique=true)
     */
    private $dept_name = '';

    /**
     * @var integer
     * @ORM\Column(name="dept_members", type="integer")
     */
    private $dept_members = 0;

    /**
     * @var integer
     * @ORM\Column(name="dept_status", type="smallint")
     */
    private $dept_status = self::STATUS_INVALID;

    /**
     * @var \DateTime
     * @ORM\Column(name="dept_created", type="datetime")
     */
    private $dept_created = null;



    /**
     * @return int
     */
    public function getDeptId()
    {
        return $this->dept_id;
    }

    /**
     * @param int $dept_id
     */
    public function setDeptId($dept_id)
    {
        $this->dept_id = $dept_id;
    }

    /**
     * @return string
     */
    public function getDeptName()
    {
        return $this->dept_name;
    }

    /**
     * @param string $dept_name
     */
    public function setDeptName($dept_name)
    {
        $this->dept_name = $dept_name;
    }

    /**
     * @return int
     */
    public function getDeptMembers()
    {
        return $this->dept_members;
    }

    /**
     * @param int $dept_members
     */
    public function setDeptMembers($dept_members)
    {
        $this->dept_members = $dept_members;
    }

    /**
     * @return int
     */
    public function getDeptStatus()
    {
        return $this->dept_status;
    }

    /**
     * @param int $dept_status
     */
    public function setDeptStatus($dept_status)
    {
        $this->dept_status = $dept_status;
    }

    /**
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_VALID => 'Valid',
            self::STATUS_INVALID => 'Invalid',
        ];
    }

    /**
     * @return string
     */
    public function getDeptStatusAsString()
    {
        $list = self::getStatusList();
        if(isset($list[$this->getDeptStatus()])) {
            return $list[$this->getDeptStatus()];
        }
        return 'Unknown';
    }

    /**
     * @return \DateTime
     */
    public function getDeptCreated()
    {
        return $this->dept_created;
    }

    /**
     * @param \DateTime $dept_created
     */
    public function setDeptCreated($dept_created)
    {
        $this->dept_created = $dept_created;
    }



}