<?php
/**
 * WechatQrcode.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * Class WechatQrcode
 * @package Admin\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="app_wx_qrcode")
 */
class WechatQrcode
{
    const TYPE_TEMP = 'QR_SCENE';
    const TYPE_FOREVER = 'QR_LIMIT_STR_SCENE';


    public static function getTypeList()
    {
        return [
            self::TYPE_TEMP => '临时二维码',
            self::TYPE_FOREVER => '永久二维码',
        ];
    }

    public function getTypeAsString()
    {
        $list = self::getTypeList();
        if (isset($list[$this->type])) {
            return $list[$this->type];
        }
        return '未知类型二维码';
    }


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
     * @ORM\Column(name="type", type="string", length=18)
     */
    private $type = self::TYPE_TEMP;

    /**
     * @var int
     *
     * @ORM\Column(name="expired", type="integer")
     */
    private $expired = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="scene", type="string", length=64)
     */
    private $scene = '';

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var Wechat
     *
     * @ORM\ManyToOne(targetEntity="Admin\Entity\Wechat", inversedBy="qrcodes")
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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * @param int $expired
     */
    public function setExpired($expired)
    {
        $this->expired = $expired;
    }

    /**
     * @return string
     */
    public function getScene()
    {
        return $this->scene;
    }

    /**
     * @param string $scene
     */
    public function setScene($scene)
    {
        $this->scene = $scene;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
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