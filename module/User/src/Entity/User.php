<?php
/**
 * User entity class
 *
 * User: leo
 */


namespace User\Entity;



use Doctrine\ORM\Mapping as ORM;


/**
 * Class User
 * This class represents a registered user.
 *
 * @package User\Entity
 * @ORM\Entity()
 * @ORM\Table(name="user")
 */
class User
{

    // User status constants
    const STATUS_ACTIVE = 1; // Actived user
    const STATUS_RETIRED = 2; // Retired user.

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="uid")
     */
    protected $uid;

    /**
     * @ORM\Column(name="email")
     */
    protected $email;

    /**
     * @ORM\Column(name="passwd")
     */
    protected $passwd;

    /**
     * @ORM\Column(name="name")
     */
    protected $name;

    /**
     * @ORM\Column(name="status")
     */
    protected $status;

    /**
     * @ORM\Column(name="created")
     */
    protected $created;

    /**
     * @ORM\Column(name="active_token")
     */
    protected $activeToken = '';

    /**
     * @ORM\Column(name="pwd_reset_token");
     */
    protected $pwdResetToken = '';

    /**
     * @ORM\Column(name="pwd_reset_token_created")
     */
    protected $pwdResetTokenCreated = 0;


    /**
     * @return integer
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param integer $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPasswd()
    {
        return $this->passwd;
    }

    /**
     * @param string $passwd
     */
    public function setPasswd($passwd)
    {
        $this->passwd = $passwd;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_RETIRED => 'Retired',
        ];
    }

    /**
     * @return string
     */
    public function getStatusAsString() {
        $list = self::getStatusList();
        if (isset($list[$this->status])) {
            return $list[$this->status];
        }
        return 'Unknown';
    }

    /**
     * @param integer $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param string $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return string
     */
    public function getActiveToken()
    {
        return $this->activeToken;
    }

    /**
     * @param string $activeToken
     */
    public function setActiveToken($activeToken)
    {
        $this->activeToken = $activeToken;
    }

    /**
     * @return string
     */
    public function getPwdResetToken()
    {
        return $this->pwdResetToken;
    }

    /**
     * @param string $pwdResetToken
     */
    public function setPwdResetToken($pwdResetToken)
    {
        $this->pwdResetToken = $pwdResetToken;
    }


    /**
     * Get the reset token creation time
     *
     * @return integer
     */
    public function getPwdResetTokenCreated()
    {
        return $this->pwdResetTokenCreated;
    }


    /**
     * Set the reset token creation time
     *
     * @param integer $created
     */
    public function setPwdResetTokenCreated($created)
    {
        $this->pwdResetTokenCreated = $created;
    }



}