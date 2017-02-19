<?php
/**
 * AclDepartment.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * Class AclDepartment
 *
 * @package Admin\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="sys_acl_department")
 */
class AclDepartment
{

    const STATUS_ALLOWED = 1;
    const STATUS_FORBIDDEN = 0;

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
     * @ORM\Column(name="dept", type="string", length=36, nullable=false)
     */
    private $dept = 0;

    /**
     * @var int
     * @ORM\Column(name="status", type="smallint")
     */
    private $status = self::STATUS_FORBIDDEN;


    /**
     * @return array
     */
    public static function getAclStatusList()
    {
        return [
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
        return '未知配置';
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
    public function getDept()
    {
        return $this->dept;
    }

    /**
     * @param string $dept
     */
    public function setDept($dept)
    {
        $this->dept = $dept;
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