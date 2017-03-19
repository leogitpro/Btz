<?php
/**
 * Tag.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace WeChat\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * Class Tag
 * @package WeChat\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="wechat_tag")
 */
class Tag
{

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="string", length=36, nullable=false)
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="tagid", type="integer")
     */
    private $tagid = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="tagname", type="string", length=45)
     */
    private $tagname = '';

    /**
     * @var int
     *
     * @ORM\Column(name="tagcount", type="integer")
     */
    private $tagcount = 0;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="WeChat\Entity\Account", inversedBy="tags")
     * @ORM\JoinColumn(name="wx", referencedColumnName="wx_id")
     */
    private $weChat;


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
     * @return int
     */
    public function getTagid()
    {
        return $this->tagid;
    }

    /**
     * @param int $tagid
     */
    public function setTagid($tagid)
    {
        $this->tagid = $tagid;
    }

    /**
     * @return string
     */
    public function getTagname()
    {
        return $this->tagname;
    }

    /**
     * @param string $tagname
     */
    public function setTagname($tagname)
    {
        $this->tagname = $tagname;
    }

    /**
     * @return int
     */
    public function getTagcount()
    {
        return $this->tagcount;
    }

    /**
     * @param int $tagcount
     */
    public function setTagcount($tagcount)
    {
        $this->tagcount = $tagcount;
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


}