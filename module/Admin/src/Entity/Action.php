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
 *
 * @package Admin\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="sys_action")
 */
class Action
{
    const ICON_DEFAULT = 'caret-right';

    const MENU_YES = 1; //
    const MENU_NO = 0; //

    const RANK_DEFAULT = 0; //


    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="action_id", type="string", length=36, nullable=false)
     */
    private $actionId;

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
     * @var Component
     *
     * @ORM\ManyToOne(targetEntity="Admin\Entity\Component", inversedBy="actions")
     * @ORM\JoinColumn(name="controller_key", referencedColumnName="controller_class")
     */
    private $component;


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
     * @return Component
     */
    public function getComponent()
    {
        return $this->component;
    }

    /**
     * @param Component $component
     */
    public function setComponent($component)
    {
        $this->component = $component;
    }




}