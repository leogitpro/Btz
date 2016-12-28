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
 * @ORM\Entity()
 * @ORM\Table(name="contact")
 */
class Contact
{

    const READ_DONE = 1; // Already read
    const READ_UNREAD = 0; // Unread

    const STATUS_NORMAL = 1; // Message normal status
    const STATUS_DELETED = 0; // Message was deleted


    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="cid")
     */
    private $cid;

    /**
     * @ORM\Column(name="email")
     */
    private $email = '';

    /**
     * @ORM\Column(name="subject")
     */
    private $subject = '';

    /**
     * @@ORM\Column(name="content")
     */
    private $content = '';

    /**
     * @ORM\Column(name="cread")
     */
    private $cread = 0;

    /**
     * @ORM\Column(name="status")
     */
    private $status = 1;

    /**
     * @ORM\Column(name="ip")
     */
    private $ip = '';

    /**
     * @ORM\Column(name="created")
     */
    private $created = '';




    /**
     * @return integer
     */
    public function getCid()
    {
        return $this->cid;
    }

    /**
     * @param integer $cid
     */
    public function setCid($cid)
    {
        $this->cid = $cid;
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
     * @return integer
     */
    public function getCread()
    {
        return $this->cread;
    }

    /**
     * @param integer $cread
     */
    public function setCread($cread)
    {
        $this->cread = $cread;
    }

    /**
     * @return array
     */
    public function getCreadList()
    {
        return [
            self::READ_UNREAD => 'Unread',
            self::READ_DONE => 'Already read',
        ];
    }

    /**
     * @return string
     */
    public function getCreadAsString()
    {
        $list = self::getReadList();
        if (isset($list[$this->cread])) {
            return $list[$this->cread];
        }
        return 'Unknown';
    }

    /**
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param integer $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param string $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }


}