<?php
/**
 * Order.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace WeChat\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * Class Order
 * @package WeChat\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="wechat_order")
 */
class Order
{
    const PAID_STATUS_DEFAULT = 0;
    const PAID_STATUS_SENT = 1;
    const PAID_STATUS_RECEIVED = 2;

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="string", length=36, nullable=false)
     */
    private $id;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="WeChat\Entity\Account", inversedBy="orders")
     * @ORM\JoinColumn(name="wx", referencedColumnName="wx_id")
     */
    private $weChat;

    /**
     * @var string
     *
     * @ORM\Column(name="no", type="string", length=14)
     */
    private $no = '';

    /**
     * @var int
     *
     * @ORM\Column(name="money", type="integer")
     */
    private $money = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="paid", type="smallint")
     */
    private $paid = self::PAID_STATUS_DEFAULT;

    /**
     * @var int
     *
     * @ORM\Column(name="second", type="integer")
     */
    private $second = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;


    /**
     * @return array
     */
    public static function getPaidList()
    {
        return [
            self::PAID_STATUS_DEFAULT => '待付款',
            self::PAID_STATUS_SENT => '已付款, 待确认',
            self::PAID_STATUS_RECEIVED => '已收款',
        ];
    }

    /**
     * @return string
     */
    public function getPaidAsString()
    {
        $list = self::getPaidList();
        if(isset($list[$this->paid])) {
            return $list[$this->paid];
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
     * @return Account
     */
    public function getWeChat()
    {
        return $this->weChat;
    }

    /**
     * @param Account $weChat
     */
    public function setWeChat($weChat)
    {
        $this->weChat = $weChat;
    }

    /**
     * @return string
     */
    public function getNo()
    {
        return $this->no;
    }

    /**
     * @param string $no
     */
    public function setNo($no)
    {
        $this->no = $no;
    }

    /**
     * @return int
     */
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * @param int $money
     */
    public function setMoney($money)
    {
        $this->money = $money;
    }

    /**
     * @return int
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * @param int $paid
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;
    }

    /**
     * @return int
     */
    public function getSecond()
    {
        return $this->second;
    }

    /**
     * @param int $second
     */
    public function setSecond($second)
    {
        $this->second = $second;
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


}