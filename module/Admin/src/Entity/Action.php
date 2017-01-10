<?php
/**
 * Action.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * Class Action
 * @package Admin\Entity
 * @ORM\Entity()
 * @ORM\Table(name="sys_action")
 */
class Action
{

    const STATUS_INVALID = 0; // Invalid
    const STATUS_VALIDITY = 1; // Validity

    const ICON_DEFAULT = 'caret-right';

    const MENU_YES = 1; //
    const MENU_NO = 0; //

    const RANK_DEFAULT = 0; //


    /**
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="action_id", type="integer")
     */
    private $actionId;

    /**
     * @var string
     * @ORM\Column(name="controller_class", type="string", length=100)
     */
    private $controllerClass = '';

    /**
     * @var string
     * @ORM\Column(name="action_key", type="string", length=45)
     */
    private $actionKey = '';

    /**
     * @var string
     * @ORM\Column(name="action_name", type="string", length=45)
     */
    private $actionName = '';

    /**
     * @var string
     * @ORM\Column(name="action_icon", type="string", length=45)
     */
    private $actionIcon = self::ICON_DEFAULT;

    /**
     * @var int
     * @ORM\Column(name="action_rank", type="integer")
     */
    private $actionRank = self::RANK_DEFAULT;

    /**
     * @var int
     * @ORM\Column(name="action_menu", type="integer")
     */
    private $actionMenu = self::MENU_NO;

    /**
     * @var int
     * @ORM\Column(name="action_status", type="integer")
     */
    private $actionStatus = self::STATUS_INVALID;

    /**
     * @var \DateTime
     * @ORM\Column(name="action_created", type="datetime")
     */
    private $actionCreated;


    /**
     * @return array
     */
    public static function getActionStatusList()
    {
        return [
            self::STATUS_INVALID => 'Invalid',
            self::STATUS_VALIDITY => 'Validity',
        ];
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
     * @return string
     */
    public function getControllerClass()
    {
        return $this->controllerClass;
    }

    /**
     * @param string $controllerClass
     */
    public function setControllerClass($controllerClass)
    {
        $this->controllerClass = $controllerClass;
    }

    /**
     * @return string
     */
    public function getActionKey()
    {
        return $this->actionKey;
    }

    /**
     * @param string $actionKey
     */
    public function setActionKey($actionKey)
    {
        $this->actionKey = $actionKey;
    }

    /**
     * @return string
     */
    public function getActionName()
    {
        return $this->actionName;
    }

    /**
     * @param string $actionName
     */
    public function setActionName($actionName)
    {
        $this->actionName = $actionName;
    }

    /**
     * @return string
     */
    public function getActionIcon()
    {
        return $this->actionIcon;
    }

    /**
     * @param string $actionIcon
     */
    public function setActionIcon($actionIcon)
    {
        $this->actionIcon = $actionIcon;
    }

    /**
     * @return int
     */
    public function getActionRank()
    {
        return $this->actionRank;
    }

    /**
     * @param int $actionRank
     */
    public function setActionRank($actionRank)
    {
        $this->actionRank = $actionRank;
    }

    /**
     * @return int
     */
    public function getActionMenu()
    {
        return $this->actionMenu;
    }

    /**
     * @param int $actionMenu
     */
    public function setActionMenu($actionMenu)
    {
        $this->actionMenu = $actionMenu;
    }

    /**
     * @return int
     */
    public function getActionStatus()
    {
        return $this->actionStatus;
    }

    /**
     * @param int $actionStatus
     */
    public function setActionStatus($actionStatus)
    {
        $this->actionStatus = $actionStatus;
    }

    /**
     * @return \DateTime
     */
    public function getActionCreated()
    {
        return $this->actionCreated;
    }

    /**
     * @param \DateTime $actionCreated
     */
    public function setActionCreated($actionCreated)
    {
        $this->actionCreated = $actionCreated;
    }


    /**
     * @return string
     */
    public function getActionStatusAsString()
    {
        $list = self::getActionStatusList();
        if (isset($list[$this->getActionStatus()])) {
            return $list[$this->getActionStatus()];
        }
        return 'Unknown';
    }



}