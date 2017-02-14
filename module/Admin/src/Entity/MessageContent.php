<?php
/**
 * MessageContent.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * Class MessageContent
 * @package Admin\Entity
 * @ORM\Entity
 * @ORM\Table(name="sys_message_content")
 */
class MessageContent
{

    const STATUS_INVALID = 0;
    const STATUS_VALIDITY = 1;


    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="id", type="string", length=36, nullable=false)
     */
    private $id;

    /**
     * @var integer
     * @ORM\Column(name="status", type="smallint")
     */
    private $status = self::STATUS_VALIDITY;

    /**
     * @var string
     * @ORM\Column(name="topic", type="string", length=128)
     */
    private $topic = '';

    /**
     * @var string
     * @ORM\Column(name="content", type="string", length=4096)
     */
    private $content = '';

    /**
     * @var \DateTime
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

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
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * @param string $topic
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
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