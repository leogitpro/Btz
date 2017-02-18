<?php
/**
 * Component.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Component
 *
 * @package Admin\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="sys_controller")
 */
class Component
{
    const MENU_YES = 1; // Is menu component
    const MENU_NO = 0; // not a menu component

    const ICON_DEFAULT = 'list';

    const RANK_DEFAULT = 0; //


    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="controller_class", type="string", length=100, nullable=false)
     */
    private $comClass;

    /**
     * @var string
     *
     * @ORM\Column(name="controller_name", type="string", length=100)
     */
    private $comName = '';

    /**
     * @var string
     *
     * @ORM\Column(name="controller_icon", type="string", length=45)
     */
    private $comIcon = self::ICON_DEFAULT;

    /**
     * @var string
     *
     * @ORM\Column(name="controller_route", type="string", length=45)
     */
    private $comRoute = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="controller_rank", type="smallint")
     */
    private $comRank = self::RANK_DEFAULT;

    /**
     * @var integer
     *
     * @ORM\Column(name="controller_menu", type="smallint")
     */
    private $comMenu = self::MENU_NO;


    /**
     * cascade: remove => 删除 component, 同步删除其下属的所有 actions
     *          persist => 添加 component, 同步添加actions中所有的对象到数据库中.
     *
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Admin\Entity\Action", mappedBy="component", cascade={"remove", "persist"})
     */
    private $actions;


    public function __construct()
    {
        $this->actions = new ArrayCollection();
    }


    /**
     * @return string
     */
    public function getComClass()
    {
        return $this->comClass;
    }

    /**
     * @param string $comClass
     */
    public function setComClass($comClass)
    {
        $this->comClass = $comClass;
    }


    /**
     * @return string
     */
    public function getComName()
    {
        return $this->comName;
    }

    /**
     * @param string $comName
     */
    public function setComName($comName)
    {
        $this->comName = $comName;
    }

    /**
     * @return string
     */
    public function getComIcon()
    {
        return $this->comIcon;
    }

    /**
     * @param string $comIcon
     */
    public function setComIcon($comIcon)
    {
        $this->comIcon = $comIcon;
    }

    /**
     * @return string
     */
    public function getComRoute()
    {
        return $this->comRoute;
    }

    /**
     * @param string $comRoute
     */
    public function setComRoute($comRoute)
    {
        $this->comRoute = $comRoute;
    }

    /**
     * @return int
     */
    public function getComRank()
    {
        return $this->comRank;
    }

    /**
     * @param int $comRank
     */
    public function setComRank($comRank)
    {
        $this->comRank = $comRank;
    }

    /**
     * @return int
     */
    public function getComMenu()
    {
        return $this->comMenu;
    }

    /**
     * @param int $comMenu
     */
    public function setComMenu($comMenu)
    {
        $this->comMenu = $comMenu;
    }

    /**
     * @return ArrayCollection
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param ArrayCollection $actions
     */
    public function setActions($actions)
    {
        $this->actions = $actions;
    }

}