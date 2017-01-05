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


use Doctrine\ORM\Mapping as ORM;


/**
 * Class Member
 *
 * @package Admin\Entity
 * @ORM\Entity()
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


    /**
     * Primary key, auto increment
     *
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="member_id", type="integer")
     */
    private $member_id;


    /**
     * Administrator email, unique
     *
     * @var string
     * @ORM\Column(name="member_email", type="string", length=45, unique=true)
     */
    private $member_email = '';


    /**
     * Administrator password, md5 value
     *
     * @var string
     * @ORM\Column(name="member_password", type="string", length=32)
     */
    private $member_password = '';


    /**
     * Administrator name.
     *
     * @var string
     * @ORM\Column(name="member_name", type="string", length=45)
     */
    private $member_name = '';


    /**
     * Administrator status, activated, retried, etc ...
     *
     * @var integer
     * @ORM\Column(name="member_status", type="smallint")
     */
    private $member_status = self::STATUS_RETRIED;


    /**
     * Administrator level.
     *
     * @var integer
     * @ORM\Column(name="member_level", type="smallint")
     */
    private $member_level = self::LEVEL_INTERIOR;


    /**
     * Administrator created. datetime
     *
     * @var \DateTime
     * @ORM\Column(name="member_created", type="datetime")
     */
    private $member_created = null;


    /**
     * Get the administrator status list
     *
     * @return array
     */
    public static function getMemberStatusList()
    {
        return [
            self::STATUS_ACTIVATED => 'Activated',
            self::STATUS_RETRIED => 'Retried',
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
            self::LEVEL_INTERIOR => 'Interior',
            self::LEVEL_JUNIOR => 'Junior',
            self::LEVEL_SENIOR => 'Senior',
            self::LEVEL_SUPERIOR => 'Superior',
        ];
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
     * @return string
     */
    public function getMemberEmail()
    {
        return $this->member_email;
    }

    /**
     * @param string $member_email
     */
    public function setMemberEmail($member_email)
    {
        $this->member_email = $member_email;
    }

    /**
     * @return string
     */
    public function getMemberPassword()
    {
        return $this->member_password;
    }

    /**
     * @param string $member_password
     */
    public function setMemberPassword($member_password)
    {
        $this->member_password = $member_password;
    }

    /**
     * @return string
     */
    public function getMemberName()
    {
        return $this->member_name;
    }

    /**
     * @param string $member_name
     */
    public function setMemberName($member_name)
    {
        $this->member_name = $member_name;
    }

    /**
     * @return int
     */
    public function getMemberStatus()
    {
        return $this->member_status;
    }

    /**
     * @param int $member_status
     */
    public function setMemberStatus($member_status)
    {
        $this->member_status = $member_status;
    }

    /**
     * @return int
     */
    public function getMemberLevel()
    {
        return $this->member_level;
    }

    /**
     * @param int $member_level
     */
    public function setMemberLevel($member_level)
    {
        $this->member_level = $member_level;
    }

    /**
     * @return \DateTime
     */
    public function getMemberCreated()
    {
        return $this->member_created;
    }

    /**
     * @param \DateTime $member_created
     */
    public function setMemberCreated($member_created)
    {
        $this->member_created = $member_created;
    }

    /**
     * Get administrator status as string
     *
     * @return string
     */
    public function getMemberStatusAsString()
    {
        $list = self::getMemberStatusList();
        if (isset($list[$this->getMemberStatus()])) {
            return $list[$this->getMemberStatus()];
        }
        return 'Unknown';
    }


    /**
     * Get administrator level as string
     *
     * @return string
     */
    public function getMemberLevelAsString()
    {
        $list = self::getMemberLevelList();
        if (isset($list[$this->getMemberLevel()])) {
            return $list[$this->getMemberLevel()];
        }
        return 'Unknown';
    }


}