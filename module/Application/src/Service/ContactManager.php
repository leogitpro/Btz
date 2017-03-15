<?php
/**
 * Contact manager
 *
 * User: leo
 */

namespace Application\Service;


use Application\Entity\Contact;
use Ramsey\Uuid\Uuid;


class ContactManager extends BaseEntityManager
{
    /**
     * Save new contact data
     *
     * @param string $email
     * @param string $subject
     * @param string $message
     * @param string $ip
     */
    public function createContact($email, $subject, $message, $ip)
    {
        $entity = new Contact();
        $entity->setId(Uuid::uuid1()->toString());
        $entity->setEmail($email);
        $entity->setSubject($subject);
        $entity->setContent($message);
        $entity->setFromIp($ip);
        $entity->setStatus(Contact::STATUS_UNREAD);
        $entity->setCreated(new \DateTime());

        $this->saveModifiedEntity($entity);
    }

}