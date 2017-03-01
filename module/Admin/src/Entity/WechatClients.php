<?php
/**
 * WechatClient.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * Class WechatClient
 * @package Admin\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="app_wx_client")
 */
class WechatClients
{

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
     * @var integer
     *
     * @ORM\Column(name="active_time", type="integer")
     */
    private $activeTime = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="expire_time", type="integer")
     */
    private $expireTime = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string", length=255)
     */
    private $domain = '';

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=255)
     */
    private $ip = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var Wechat
     *
     * @ORM\ManyToOne(targetEntity="Admin\Entity\Wechat", inversedBy="clients")
     * @ORM\JoinColumn(name="wx", referencedColumnName="wx_id")
     */
    private $wechat;


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
     * @return int
     */
    public function getActiveTime()
    {
        return $this->activeTime;
    }

    /**
     * @param int $activeTime
     */
    public function setActiveTime($activeTime)
    {
        $this->activeTime = $activeTime;
    }

    /**
     * @return int
     */
    public function getExpireTime()
    {
        return $this->expireTime;
    }

    /**
     * @param int $expireTime
     */
    public function setExpireTime($expireTime)
    {
        $this->expireTime = $expireTime;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return Wechat
     */
    public function getWechat()
    {
        return $this->wechat;
    }

    /**
     * @param Wechat $wechat
     */
    public function setWechat($wechat)
    {
        $this->wechat = $wechat;
    }



}