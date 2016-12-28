<?php
/**
 * Contact manager
 *
 * User: leo
 */

namespace Application\Service;


use Application\Entity\Contact;
use Doctrine\ORM\EntityManager;
use Zend\Log\Logger;

class ContactManager
{

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Logger
     */
    private $logger;


    /**
     * ContactManager constructor.
     *
     * @param EntityManager $entityManager
     * @param Logger $logger
     */
    public function __construct(EntityManager $entityManager, Logger $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }


    /**
     * Save new contact data
     *
     * @param string $email
     * @param string $subject
     * @param string $message
     * @param string $ip
     * @return Contact
     */
    public function createContactMessage($email, $subject, $message, $ip)
    {
        $contact = new Contact();
        $contact->setEmail($email);
        $contact->setSubject($subject);
        $contact->setContent($message);
        $contact->setIp($ip);
        $contact->setCread(Contact::READ_UNREAD);
        $contact->setStatus(Contact::STATUS_NORMAL);
        $contact->setCreated(date('Y-m-d H:i:s'));

        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        $this->logger->debug(__METHOD__ . PHP_EOL . 'New contact message have saved to database.');

        return $contact;
    }

}