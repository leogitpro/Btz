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

    const STATUS_READ = 1; // Read
    const STATUS_UNREAD = 0; // Unread

    const STATUS_NORMAL = 1; // Message normal status
    const STATUS_DELETED = 0; // Message was deleted


    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="contact_id")
     */
    private $contact_id;

    /**
     * @ORM\Column(name="contact_email")
     */
    private $contact_email = '';

    /**
     * @ORM\Column(name="contact_subject")
     */
    private $contact_subject = '';

    /**
     * @ORM\Column(name="contact_content")
     */
    private $contact_content = '';

    /**
     * @ORM\Column(name="contact_is_read")
     */
    private $contact_is_read = 0;

    /**
     * @ORM\Column(name="contact_status")
     */
    private $contact_status = 1;

    /**
     * @ORM\Column(name="contact_from_ip")
     */
    private $contact_from_ip = '';

    /**
     * @ORM\Column(name="contact_created")
     */
    private $contact_created = '';


    /**
     * @return integer
     */
    public function getContactId()
    {
        return $this->contact_id;
    }

    /**
     * @param integer $contact_id
     */
    public function setContactId($contact_id)
    {
        $this->contact_id = $contact_id;
    }

    /**
     * @return string
     */
    public function getContactEmail()
    {
        return $this->contact_email;
    }

    /**
     * @param string $contact_email
     */
    public function setContactEmail($contact_email)
    {
        $this->contact_email = $contact_email;
    }

    /**
     * @return string
     */
    public function getContactSubject()
    {
        return $this->contact_subject;
    }

    /**
     * @param string $contact_subject
     */
    public function setContactSubject($contact_subject)
    {
        $this->contact_subject = $contact_subject;
    }

    /**
     * @return string
     */
    public function getContactContent()
    {
        return $this->contact_content;
    }

    /**
     * @param string $contact_content
     */
    public function setContactContent($contact_content)
    {
        $this->contact_content = $contact_content;
    }

    /**
     * @return integer
     */
    public function getContactIsRead()
    {
        return $this->contact_is_read;
    }

    /**
     * @param integer $contact_is_read
     */
    public function setContactIsRead($contact_is_read)
    {
        $this->contact_is_read = $contact_is_read;
    }

    /**
     * @return array
     */
    public static function getContactIsReadList()
    {
        return [
            self::STATUS_UNREAD => 'Unread',
            self::STATUS_READ => 'Read',
        ];
    }

    /**
     * @return string
     */
    public function getContactIsReadAsString()
    {
        $list = self::getContactIsReadList();
        if (isset($list[$this->contact_is_read])) {
            return $list[$this->contact_is_read];
        }
        return 'Unknown';
    }

    /**
     * @return integer
     */
    public function getContactStatus()
    {
        return $this->contact_status;
    }

    /**
     * @param integer $contact_status
     */
    public function setContactStatus($contact_status)
    {
        $this->contact_status = $contact_status;
    }

    /**
     * @return string
     */
    public function getContactFromIp()
    {
        return $this->contact_from_ip;
    }

    /**
     * @param string $contact_from_ip
     */
    public function setContactFromIp($contact_from_ip)
    {
        $this->contact_from_ip = $contact_from_ip;
    }

    /**
     * @return string
     */
    public function getContactCreated()
    {
        return $this->contact_created;
    }

    /**
     * @param string $contact_created
     */
    public function setContactCreated($contact_created)
    {
        $this->contact_created = $contact_created;
    }

}