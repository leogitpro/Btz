<?php
/**
 * AclMember.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * Class AclMember
 * @package Admin\Entity
 * @ORM\Entity()
 * @ORM\Table(name="sys_acl_member")
 */
class AclMember
{

    const STATUS_VALIDITY = 1;
    const STATUS_INVALID = 0;

    /**
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    private $id = 0;

    /**
     * @var int
     * @ORM\Column(name="action_id", type="integer")
     */
    private $actionId = 0;

    /**
     * @var int
     * @ORM\Column(name="member_id", type="integer")
     */
    private $memberId = 0;

    /**
     * @var int
     * @ORM\Column(name="status", type="smallint")
     */
    private $status = self::STATUS_INVALID;

    /**
     * @var \DateTime
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;


    /**
     * @return array
     */
    public static function getAclStatusList()
    {
        return [
            self::STATUS_INVALID => 'Invalid',
            self::STATUS_VALIDITY => 'Validity',
        ];
    }


    /**
     * @return string
     */
    public function getStatusAsString()
    {
        $list = self::getAclStatusList();
        if (isset($list[$this->getStatus()])) {
            return $list[$this->getStatus()];
        }
        return 'Unknown';
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
    public function getActionId()
    {
        return $this->actionId;
    }

    /**
     * @param int $actionId
     */
    public function setActionId($actionId)
    {
        $this->actionId = $actionId;
    }

    /**
     * @return int
     */
    public function getMemberId()
    {
        return $this->memberId;
    }

    /**
     * @param int $memberId
     */
    public function setMemberId($memberId)
    {
        $this->memberId = $memberId;
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