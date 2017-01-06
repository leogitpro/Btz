<?php
/**
 * Entity for department and member relationship
 *
 * User: leo
 */

namespace Admin\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * Class DepartmentMember
 * @package Admin\Entity
 * @ORM\Entity()
 * @ORM\Table(name="sys_department_member")
 */
class DepartmentMember
{

    const STATUS_INVALID = 0; // Status invalid
    const STATUS_VALID = 1; // Status valid

    /**
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var integer
     * @ORM\Column(name="dept_id", type="integer")
     */
    private $dept_id = 0;

    /**
     * @var integer
     * @ORM\Column(name="member_id", type="integer")
     */
    private $member_id = 0;

    /**
     * @var integer
     * @ORM\Column(name="status", type="smallint")
     */
    private $status = self::STATUS_INVALID;

    /**
     * @var \DateTime
     * @ORM\Column(name="created", type="datetime")
     */
    private $created = null;



    /**
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_INVALID => 'Invalid',
            self::STATUS_VALID => 'Valid',
        ];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

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
     * @return int
     */
    public function getMemberId()
    {
        return $this->member_id;
    }

    /**
     * @param int $member_id
     */
    public function setMemberId($member_id)
    {
        $this->member_id = $member_id;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatusAsString()
    {
        $list = self::getStatusList();
        if (isset($list[$this->getStatus()])) {
            return $list[$this->getStatus()];
        }
        return 'Unknown';
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }




}