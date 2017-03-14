<?php
/**
 * WeChat.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class WeChat
 * @package Admin\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="app_wx")
 */
class WeChat
{

    const STATUS_UNCHECK = 0;
    const STATUS_CHECKED = 1;


    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="wx_id", type="integer")
     */
    private $wxId;

    /**
     * @var string
     *
     * @ORM\Column(name="wx_appid", type="string", length=45, nullable=false)
     */
    private $wxAppId = '';

    /**
     * @var string
     *
     * @ORM\Column(name="wx_appsecret", type="string", length=255, nullable=false)
     */
    private $wxAppSecret = '';

    /**
     * @var string
     *
     * @ORM\Column(name="wx_access_token", type="string", length=512)
     */
    private $wxAccessToken = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="wx_access_token_expired", type="integer")
     */
    private $wxAccessTokenExpired = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="wx_expired", type="integer")
     */
    private $wxExpired = 0;


    /**
     * @var integer
     *
     * @ORM\Column(name="wx_checked", type="integer")
     */
    private $wxChecked = self::STATUS_UNCHECK;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="wx_created", type="datetime")
     */
    private $wxCreated;


    /**
     * @var Member
     *
     * @ORM\OneToOne(targetEntity="Admin\Entity\Member")
     * @ORM\JoinColumn(name="member", referencedColumnName="member_id")
     */
    private $member;


    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Admin\Entity\WeChatClient", mappedBy="weChat")
     */
    private $clients;


    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Admin\Entity\WeChatTag", mappedBy="weChat")
     */
    private $tags;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Admin\Entity\WeChatMenu", mappedBy="weChat")
     */
    private $menus;


    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Admin\Entity\WeChatQrCode", mappedBy="weChat")
     */
    private $qrCodes;



    public function __construct()
    {
        $this->clients = new ArrayCollection();
        $this->menus = new ArrayCollection();
        $this->qrCodes = new ArrayCollection();
    }


    /**
     * @return int
     */
    public function getWxId()
    {
        return $this->wxId;
    }

    /**
     * @param int $wxId
     */
    public function setWxId($wxId)
    {
        $this->wxId = $wxId;
    }

    /**
     * @return string
     */
    public function getWxAppId()
    {
        return $this->wxAppId;
    }

    /**
     * @param string $wxAppId
     */
    public function setWxAppId($wxAppId)
    {
        $this->wxAppId = $wxAppId;
    }

    /**
     * @return string
     */
    public function getWxAppSecret()
    {
        return $this->wxAppSecret;
    }

    /**
     * @param string $wxAppSecret
     */
    public function setWxAppSecret($wxAppSecret)
    {
        $this->wxAppSecret = $wxAppSecret;
    }

    /**
     * @return string
     */
    public function getWxAccessToken()
    {
        return $this->wxAccessToken;
    }

    /**
     * @param string $wxAccessToken
     */
    public function setWxAccessToken($wxAccessToken)
    {
        $this->wxAccessToken = $wxAccessToken;
    }

    /**
     * @return int
     */
    public function getWxAccessTokenExpired()
    {
        return $this->wxAccessTokenExpired;
    }

    /**
     * @param int $wxAccessTokenExpired
     */
    public function setWxAccessTokenExpired($wxAccessTokenExpired)
    {
        $this->wxAccessTokenExpired = $wxAccessTokenExpired;
    }

    /**
     * @return int
     */
    public function getWxExpired()
    {
        return $this->wxExpired;
    }

    /**
     * @param int $wxExpired
     */
    public function setWxExpired($wxExpired)
    {
        $this->wxExpired = $wxExpired;
    }

    /**
     * @return int
     */
    public function getWxChecked()
    {
        return $this->wxChecked;
    }

    /**
     * @param int $wxChecked
     */
    public function setWxChecked($wxChecked)
    {
        $this->wxChecked = $wxChecked;
    }

    /**
     * @return \DateTime
     */
    public function getWxCreated()
    {
        return $this->wxCreated;
    }

    /**
     * @param \DateTime $wxCreated
     */
    public function setWxCreated($wxCreated)
    {
        $this->wxCreated = $wxCreated;
    }

    /**
     * @return Member
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * @param Member $member
     */
    public function setMember($member)
    {
        $this->member = $member;
    }

    /**
     * @return ArrayCollection
     */
    public function getClients()
    {
        return $this->clients;
    }

    /**
     * @param ArrayCollection $clients
     */
    public function setClients($clients)
    {
        $this->clients = $clients;
    }

    /**
     * @return ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param ArrayCollection $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return ArrayCollection
     */
    public function getMenus()
    {
        return $this->menus;
    }

    /**
     * @param ArrayCollection $menus
     */
    public function setMenus($menus)
    {
        $this->menus = $menus;
    }

    /**
     * @return ArrayCollection
     */
    public function getQrCodes()
    {
        return $this->qrCodes;
    }

    /**
     * @param ArrayCollection $qrCodes
     */
    public function setQrCodes($qrCodes)
    {
        $this->qrCodes = $qrCodes;
    }

}