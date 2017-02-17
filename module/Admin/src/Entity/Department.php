<?php
/**
 * Department.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Department
 *
 * @package Admin\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="sys_department")
 */
class Department
{

    const STATUS_VALID = 1;
    const STATUS_INVALID = 0;

    const DEFAULT_DEPT_ID = 'ad739904-f423-11e6-b154-acbc32bf6185';

    /**
     * Primary key, UUID
     *
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="dept_id", type="string", length=36, nullable=false)
     */
    private $deptId;

    /**
     * @var string
     *
     * @ORM\Column(name="dept_name", type="string", length=45, unique=true)
     */
    private $deptName = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="dept_status", type="smallint")
     */
    private $deptStatus = self::STATUS_INVALID;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dept_created", type="datetime")
     */
    private $deptCreated = null;


    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Admin\Entity\Member", mappedBy="depts")
     */
    private $members;


    public function __construct()
    {
        $this->members = new ArrayCollection();
    }


    /**
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_VALID => '已激活',
            self::STATUS_INVALID => '未激活',
        ];
    }

    /**
     * @return string
     */
    public function getDeptStatusAsString()
    {
        $list = self::getStatusList();
        if(isset($list[$this->deptStatus])) {
            return $list[$this->deptStatus];
        }
        return '未知';
    }

    /**
     * @return string
     */
    public function getDeptId()
    {
        return $this->deptId;
    }

    /**
     * @param string $deptId
     */
    public function setDeptId($deptId)
    {
        $this->deptId = $deptId;
    }

    /**
     * @return string
     */
    public function getDeptName()
    {
        return $this->deptName;
    }

    /**
     * @param string $deptName
     */
    public function setDeptName($deptName)
    {
        $this->deptName = $deptName;
    }

    /**
     * @return int
     */
    public function getDeptStatus()
    {
        return $this->deptStatus;
    }

    /**
     * @param int $deptStatus
     */
    public function setDeptStatus($deptStatus)
    {
        $this->deptStatus = $deptStatus;
    }

    /**
     * @return \DateTime
     */
    public function getDeptCreated()
    {
        return $this->deptCreated;
    }

    /**
     * @param \DateTime $deptCreated
     */
    public function setDeptCreated($deptCreated)
    {
        $this->deptCreated = $deptCreated;
    }

    /**
     * @return ArrayCollection
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * @param ArrayCollection $members
     */
    public function setMembers($members)
    {
        $this->members = $members;
    }

}