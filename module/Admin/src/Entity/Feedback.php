<?php
/**
 * Feedback.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * Class Feedback
 * @package Admin\Entity
 * @ORM\Entity
 * @ORM\Table(name="sys_feedback")
 */
class Feedback
{

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="id", type="string", length=36, nullable=false)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var string
     *
     * @ORM\Column(name="reply", type="text")
     */
    private $reply = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @var Member
     *
     * @ORM\ManyToOne(targetEntity="Admin\Entity\Member")
     * @ORM\JoinColumn(name="sender", referencedColumnName="member_id")
     */
    private $sender;

    /**
     * @var Member
     *
     * @ORM\ManyToOne(targetEntity="Admin\Entity\Member")
     * @ORM\JoinColumn(name="replier", referencedColumnName="member_id")
     */
    private $replier;

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

    /**
     * @return string
     */
    public function getReply()
    {
        return $this->reply;
    }

    /**
     * @param string $reply
     */
    public function setReply($reply)
    {
        $this->reply = $reply;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return Member
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param Member $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     * @return Member
     */
    public function getReplier()
    {
        return $this->replier;
    }

    /**
     * @param Member $replier
     */
    public function setReplier($replier)
    {
        $this->replier = $replier;
    }


}