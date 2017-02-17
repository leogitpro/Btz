<?php
/**
 * Member.php
 *
 * Table sys_member entity class
 *
 *
 * @author: leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Member
 *
 * @package Admin\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="sys_member")
 */
class Member
{

    // Administrator status
    const STATUS_ACTIVATED = 1; // Activated
    const STATUS_RETRIED = 2; // Retried


    // Administrator level
    const LEVEL_SUPERIOR = 9; // Superior
    const LEVEL_SENIOR = 5; // Senior
    const LEVEL_JUNIOR = 1; // Junior
    const LEVEL_INTERIOR = 0; //Interior

    const DEFAULT_MEMBER_ID = 'be152a3e-f423-11e6-a4a4-acbc32bf6185';


    /**
     * Primary key, UUID
     *
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="member_id", type="string", length=36, nullable=false)
     */
    private $memberId;


    /**
     * Administrator email, unique
     *
     * @var string
     *
     * @ORM\Column(name="member_email", type="string", length=45, unique=true)
     */
    private $memberEmail = '';


    /**
     * Administrator password, md5 value
     *
     * @var string
     *
     * @ORM\Column(name="member_password", type="string", length=32)
     */
    private $memberPassword = '';


    /**
     * Administrator name.
     *
     * @var string
     *
     * @ORM\Column(name="member_name", type="string", length=45)
     */
    private $memberName = '';


    /**
     * Administrator status, activated, retried, etc ...
     *
     * @var integer
     *
     * @ORM\Column(name="member_status", type="smallint")
     */
    private $memberStatus = self::STATUS_RETRIED;


    /**
     * Administrator level.
     *
     * @var integer
     *
     * @ORM\Column(name="member_level", type="smallint")
     */
    private $memberLevel = self::LEVEL_INTERIOR;


    /**
     * Administrator created. datetime
     *
     * @var \DateTime
     *
     * @ORM\Column(name="member_created", type="datetime")
     */
    private $memberCreated = null;


    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Admin\Entity\Department", inversedBy="members")
     * @ORM\JoinTable(
     *     name="sys_department_member",
     *     joinColumns={@ORM\JoinColumn(name="member", referencedColumnName="member_id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="dept", referencedColumnName="dept_id")}
     * )
     */
    private $depts;


    public function __construct()
    {
        $this->depts = new ArrayCollection();
    }


    /**
     * Get the administrator status list
     *
     * @return array
     */
    public static function getMemberStatusList()
    {
        return [
            self::STATUS_ACTIVATED => '已激活',
            self::STATUS_RETRIED => '被锁定',
        ];
    }

    /**
     * Get the administrator level list
     *
     * @return array
     */
    public static function getMemberLevelList()
    {
        return [
            self::LEVEL_INTERIOR => '初级',
            self::LEVEL_JUNIOR => '中级',
            self::LEVEL_SENIOR => '高级',
            self::LEVEL_SUPERIOR => '超级',
        ];
    }

    /**
     * Get administrator status as string
     *
     * @return string
     */
    public function getMemberStatusAsString()
    {
        $list = self::getMemberStatusList();
        if (isset($list[$this->memberStatus])) {
            return $list[$this->memberStatus];
        }
        return '未知';
    }


    /**
     * Get administrator level as string
     *
     * @return string
     */
    public function getMemberLevelAsString()
    {
        $list = self::getMemberLevelList();
        if (isset($list[$this->memberLevel])) {
            return $list[$this->memberLevel];
        }
        return '未知';
    }



    /**
     * @return string
     */
    public function getMemberId()
    {
        return $this->memberId;
    }

    /**
     * @param string $memberId
     */
    public function setMemberId($memberId)
    {
        $this->memberId = $memberId;
    }

    /**
     * @return string
     */
    public function getMemberEmail()
    {
        return $this->memberEmail;
    }

    /**
     * @param string $memberEmail
     */
    public function setMemberEmail($memberEmail)
    {
        $this->memberEmail = $memberEmail;
    }

    /**
     * @return string
     */
    public function getMemberPassword()
    {
        return $this->memberPassword;
    }

    /**
     * @param string $memberPassword
     */
    public function setMemberPassword($memberPassword)
    {
        $this->memberPassword = $memberPassword;
    }

    /**
     * @return string
     */
    public function getMemberName()
    {
        return $this->memberName;
    }

    /**
     * @param string $memberName
     */
    public function setMemberName($memberName)
    {
        $this->memberName = $memberName;
    }

    /**
     * @return int
     */
    public function getMemberStatus()
    {
        return $this->memberStatus;
    }

    /**
     * @param int $memberStatus
     */
    public function setMemberStatus($memberStatus)
    {
        $this->memberStatus = $memberStatus;
    }

    /**
     * @return int
     */
    public function getMemberLevel()
    {
        return $this->memberLevel;
    }

    /**
     * @param int $memberLevel
     */
    public function setMemberLevel($memberLevel)
    {
        $this->memberLevel = $memberLevel;
    }

    /**
     * @return \DateTime
     */
    public function getMemberCreated()
    {
        return $this->memberCreated;
    }

    /**
     * @param \DateTime $memberCreated
     */
    public function setMemberCreated($memberCreated)
    {
        $this->memberCreated = $memberCreated;
    }

    /**
     * @return ArrayCollection
     */
    public function getDepts()
    {
        return $this->depts;
    }

    /**
     * @param ArrayCollection $depts
     */
    public function setDepts($depts)
    {
        $this->depts = $depts;
    }




}