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
 *
 * @package Admin\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="sys_acl_member")
 */
class AclMember
{

    const STATUS_ALLOWED = 1;
    const STATUS_FORBIDDEN = 2;
    const STATUS_DEFAULT = 0;


    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="action", type="string", length=36, nullable=false)
     */
    private $action = '';

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="member", type="string", length=36, nullable=false)
     */
    private $member = '';

    /**
     * @var int
     * @ORM\Column(name="status", type="smallint")
     */
    private $status = self::STATUS_DEFAULT;


    /**
     * @return array
     */
    public static function getAclStatusList()
    {
        return [
            self::STATUS_DEFAULT => '暂不设定',
            self::STATUS_ALLOWED => '允许访问',
            self::STATUS_FORBIDDEN => '禁止访问',
        ];
    }


    /**
     * @return string
     */
    public function getStatusAsString()
    {
        $list = self::getAclStatusList();
        if (isset($list[$this->status])) {
            return $list[$this->status];
        }
        return '未知设定';
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * @param string $member
     */
    public function setMember($member)
    {
        $this->member = $member;
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


    

}