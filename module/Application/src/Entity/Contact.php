<?php
/**
 * Contact entity
 *
 * User: leo
 */

namespace Application\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * Class Contact
 *
 * @package Application\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="app_contact")
 */
class Contact
{

    const STATUS_READ = 1; // Read
    const STATUS_UNREAD = 0; // Unread


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
     * @ORM\Column(name="email", type="string", length=45)
     */
    private $email = '';

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=128)
     */
    private $subject = '';

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content = '';

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status = self::STATUS_UNREAD;


    /**
     * @var string
     *
     * @ORM\Column(name="from_ip", type="string", length=20)
     */
    private $fromIp = '';

    /**
     * @var \DateTime
     *
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
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
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
    public function getFromIp()
    {
        return $this->fromIp;
    }

    /**
     * @param string $fromIp
     */
    public function setFromIp($fromIp)
    {
        $this->fromIp = $fromIp;
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