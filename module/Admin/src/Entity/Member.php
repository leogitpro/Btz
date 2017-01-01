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

    // Member account status
    const STATUS_ACTIVATED = 1; // Activated
    const STATUS_RETRIED = 2; // Retried


    /**
     * Primary key, auto increment
     *
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="member_id")
     */
    private $member_id;

    /**
     * Administrator email, unique
     *
     * @var string
     * @ORM\Column(name="member_email")
     */
    private $member_email;

    /**
     * Administrator password, md5 value
     *
     * @var string
     * @ORM\Column(name="member_password")
     */
    private $member_password;

    /**
     * Administrator name.
     *
     * @var string
     * @ORM\Column(name="member_name")
     */
    private $member_name;

    /**
     * Administrator status, activated, retried, etc ...
     *
     * @var integer
     * @ORM\Column(name="member_status")
     */
    private $member_status;

    /**
     * Administrator created. datetime
     *
     * @var string
     * @ORM\Column(name="member_created")
     */
    private $member_created;


    /**
     * Get the member status list
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
     * @return string
     */
    public function getMemberCreated()
    {
        return $this->member_created;
    }

    /**
     * @param string $member_created
     */
    public function setMemberCreated($member_created)
    {
        $this->member_created = $member_created;
    }

    /**
     * Get member status as string
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


}