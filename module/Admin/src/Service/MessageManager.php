<?php
/**
 * MessageManager.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Service;


use Admin\Entity\MessageBox;
use Admin\Entity\MessageContent;

class MessageManager extends BaseEntityManager
{





    /**
     * Get a message content
     *
     * @param integer $message_id
     * @return MessageContent
     */
    public function getMessageContent($message_id)
    {
        $entity = $this->entityManager->getRepository(MessageContent::class)->find($message_id);
        if (null == $entity) {
            return null;
        }
        if (MessageContent::STATUS_VALIDITY != $entity->getStatus()) {
            return null;
        }

        return $entity;
    }


    /**
     * Create a new message content.
     *
     * @param string $topic
     * @param string $content
     * @param integer $sender
     * @param array $receiver
     * @param integer $type
     *
     * @return MessageContent
     */
    public function createNewMessage($topic, $content, $sender, $receiver, $type)
    {
        $dt = new \DateTime();

        $message = new MessageContent();
        $message->setTopic($topic);
        $message->setContent($content);
        $message->setStatus(MessageContent::STATUS_VALIDITY);
        $message->setCreated($dt);

        $message = $this->saveModifiedEntity($message);
        $messageId = $message->getId();

        $total = 0;

        if ($messageId < 1) {
            return $total;
        }
        $i = 0;
        foreach ($receiver as $target) {
            $entity = new MessageBox();
            $entity->setMessageId($messageId);
            $entity->setSender($sender);
            $entity->setReceiver($target);
            $entity->setType($type);
            $entity->setCreated($dt);

            $this->entityManager->persist($entity);
            $total++;
            $i++;
            if (20 == $i) {
                $this->entityManager->flush();
                $i = 0;
            }
        }
        if ($i) {
            $this->entityManager->flush();
        }
        return $total;
    }
}