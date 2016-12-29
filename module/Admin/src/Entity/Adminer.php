<?php
/**
 * Administrator entity
 *
 * User: leo
 */

namespace Admin\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * Class Adminer
 * @package Admin\Entity
 * @ORM\Entity()
 * @ORM\Table(name="sys_adminer")
 */
class Adminer
{

    const STATUS_ACTIVE = 1; // Administrator status ok.
    const STATUS_RETRIED = 2; // Administrator was retried.


    /**
     * Primary key
     *
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="admin_id")
     */
    private $admin_id;

    /**
     * Administrator email
     *
     * @var string
     * @ORM\Column(name="admin_email")
     */
    private $admin_email;

    /**
     * Administrator password
     *
     * @var string
     * @ORM\Column(name="admin_passwd")
     */
    private $admin_passwd;

    /**
     * Administrator name
     *
     * @var string
     * @ORM\Column(name="admin_name")
     */
    private $admin_name;

    /**
     * Administrator status
     *
     * @var integer
     * @ORM\Column(name="admin_status")
     */
    private $admin_status;


    /**
     * Get status list
     *
     * @return array
     */
    public static function getAdminStatusList()
    {
        return [
            self::STATUS_ACTIVE => 'Activated',
            self::STATUS_RETRIED => 'Retried',
        ];
    }

    /**
     * Get status string
     *
     * @return string
     */
    public function getAdminStatusAsString()
    {
        $list = self::getAdminStatusList();
        if (isset($list[$this->getAdminStatus()])) {
            return $list[$this->getAdminStatus()];
        }
        return 'Unknown';
    }


    /**
     * @return int
     */
    public function getAdminId()
    {
        return $this->admin_id;
    }

    /**
     * @param int $admin_id
     */
    public function setAdminId($admin_id)
    {
        $this->admin_id = $admin_id;
    }

    /**
     * @return string
     */
    public function getAdminEmail()
    {
        return $this->admin_email;
    }

    /**
     * @param string $admin_email
     */
    public function setAdminEmail($admin_email)
    {
        $this->admin_email = $admin_email;
    }

    /**
     * @return string
     */
    public function getAdminPasswd()
    {
        return $this->admin_passwd;
    }

    /**
     * @param string $admin_passwd
     */
    public function setAdminPasswd($admin_passwd)
    {
        $this->admin_passwd = $admin_passwd;
    }

    /**
     * @return string
     */
    public function getAdminName()
    {
        return $this->admin_name;
    }

    /**
     * @param string $admin_name
     */
    public function setAdminName($admin_name)
    {
        $this->admin_name = $admin_name;
    }

    /**
     * @return int
     */
    public function getAdminStatus()
    {
        return $this->admin_status;
    }

    /**
     * @param int $admin_status
     */
    public function setAdminStatus($admin_status)
    {
        $this->admin_status = $admin_status;
    }


}