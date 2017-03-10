<?php
/**
 * WeChatMenu.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * Class WeChatMenu
 * @package Admin\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="app_wx_menu")
 */
class WeChatMenu
{

    const TYPE_DEFAULT = 0;
    const TYPE_CONDITIONAL = 1;

    const STATUS_ACTIVATED = 1;
    const STATUS_RETIRED = 0;

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="string", length=36, nullable=false)
     */
    private $id;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=45)
     */
    private $name = '';


    /**
     * @var string
     *
     * @ORM\Column(name="menu", type="text")
     */
    private $menu = '';

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type = self::TYPE_DEFAULT;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status = self::STATUS_RETIRED;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @var WeChat
     *
     * @ORM\ManyToOne(targetEntity="Admin\Entity\WeChat", inversedBy="clients")
     * @ORM\JoinColumn(name="wx", referencedColumnName="wx_id")
     */
    private $weChat;


    /**
     * @return array
     */
    public static function getTypeList()
    {
        return [
            self::TYPE_DEFAULT => '自定义菜单',
            self::TYPE_CONDITIONAL => '个性化菜单',
        ];
    }

    /**
     * @return string
     */
    public function getTypeAsString()
    {
        $list = self::getTypeList();
        if (isset($list[$this->type])) {
            return $list[$this->type];
        }
        return '未知类型菜单';
    }


    /**
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVATED => '使用中',
            self:: STATUS_RETIRED => '未使用',
        ];
    }

    /**
     * @return string
     */
    public function getStatusAsString()
    {
        $list = self::getStatusList();
        if (isset($list[$this->status])) {
            return $list[$this->status];
        }
        return '未知状态';
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return string
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @param string $menu
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return WeChat
     */
    public function getWeChat()
    {
        return $this->weChat;
    }

    /**
     * @param WeChat $weChat
     */
    public function setWeChat($weChat)
    {
        $this->weChat = $weChat;
    }


}