<?php
/**
 * Invoice.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace WeChat\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * Class Invoice
 * @package WeChat\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="wechat_invoice")
 */
class Invoice
{
    const STATUS_INVOICE_APPLY = 0;
    const STATUS_INVOICE_PRINT = 1;
    const STATUS_INVOICE_DELIVER = 2;

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
     * @ORM\ManyToOne(targetEntity="WeChat\Entity\Account", inversedBy="invoices")
     * @ORM\JoinColumn(name="wx", referencedColumnName="wx_id")
     */
    private $weChat;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=100)
     */
    private $title = '';

    /**
     * @var string
     *
     * @ORM\Column(name="receiver", type="string", length=45)
     */
    private $receiver = '';

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=45)
     */
    private $phone = '';

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=100)
     */
    private $address = '';

    /**
     * @var int
     *
     * @ORM\Column(name="money", type="integer")
     */
    private $money = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="string", length=255)
     */
    private $note = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;


    /**
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_INVOICE_APPLY => '已申请',
            self::STATUS_INVOICE_PRINT => '打印中',
            self::STATUS_INVOICE_DELIVER => '已邮寄',
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @param string $receiver
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
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
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
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