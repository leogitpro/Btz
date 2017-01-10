<?php
/**
 * Component.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * Class Component
 * @package Admin\Entity
 * @ORM\Entity()
 * @ORM\Table(name="sys_controller")
 */
class Component
{

    const STATUS_VALIDITY = 1; // Validity component
    const STATUS_INVALID = 0; // Invalid component

    const MENU_YES = 1; // Is menu component
    const MENU_NO = 0; // not a menu component

    const ICON_DEFAULT = 'list';

    const RANK_DEFAULT = 0; //


    /**
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="controller_id", type="integer")
     */
    private $comId;

    /**
     * @var string
     * @ORM\Column(name="controller_class", type="string", length=100)
     */
    private $comClass;

    /**
     * @var string
     * @ORM\Column(name="controller_name", type="string", length=100)
     */
    private $comName = '';

    /**
     * @var string
     * @ORM\Column(name="controller_icon", type="string", length=45)
     */
    private $comIcon = self::ICON_DEFAULT;

    /**
     * @var string
     * @ORM\Column(name="controller_route", type="string", length=45)
     */
    private $comRoute = '';

    /**
     * @var integer
     * @ORM\Column(name="controller_rank", type="smallint")
     */
    private $comRank = self::RANK_DEFAULT;

    /**
     * @var integer
     * @ORM\Column(name="controller_menu", type="smallint")
     */
    private $comMenu = self::MENU_NO;

    /**
     * @var integer
     * @ORM\Column(name="controller_status", type="smallint")
     */
    private $comStatus = self::STATUS_INVALID;

    /**
     * @var \DateTime
     * @ORM\Column(name="controller_created", type="datetime")
     */
    private $comCreated;



    /**
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_INVALID => 'Invalid',
            self::STATUS_VALIDITY => 'Validity',
        ];
    }


    /**
     * @return string
     */
    public function getComStatusAsString()
    {
        $list = self::getStatusList();
        if (isset($list[$this->getComStatus()])) {
            return $list[$this->getComStatus()];
        }
        return 'Unknown';
    }

    /**
     * @return int
     */
    public function getComId()
    {
        return $this->comId;
    }

    /**
     * @param int $comId
     */
    public function setComId($comId)
    {
        $this->comId = $comId;
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
     * @return int
     */
    public function getComStatus()
    {
        return $this->comStatus;
    }

    /**
     * @param int $comStatus
     */
    public function setComStatus($comStatus)
    {
        $this->comStatus = $comStatus;
    }

    /**
     * @return \DateTime
     */
    public function getComCreated()
    {
        return $this->comCreated;
    }

    /**
     * @param \DateTime $comCreated
     */
    public function setComCreated($comCreated)
    {
        $this->comCreated = $comCreated;
    }




}