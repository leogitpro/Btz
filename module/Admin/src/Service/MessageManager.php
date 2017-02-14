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
use Ramsey\Uuid\Uuid;

class MessageManager extends BaseEntityManager
{

    /**
     * @param int $receiver_id
     * @return int
     */
    public function getInBoxMessagesCount($receiver_id)
    {
        return $this->getEntitiesCount(MessageBox::class, 'id', [
            'receiver = :receiverId AND t.receiverStatus != :status'
        ], ['receiverId' => $receiver_id, 'status' => MessageBox::STATUS_RECEIVER_DELETED]);
    }


    /**
     * @param int $sender_id
     * @return int
     */
    public function getOutBoxMessagesCount($sender_id)
    {
        return $this->getEntitiesCount(MessageBox::class, 'id', [
            'sender = :senderId AND t.senderStatus = :status'
        ], ['senderId' => $sender_id, 'status' => MessageBox::STATUS_SENDER_SENT]);
    }


    /**
     * @param int $receiver_id
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getInBoxMessagesByLimitPage($receiver_id, $page = 1, $size = 10)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('t')->from(MessageBox::class, 't')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq('t.receiver', '?1'),
                    $qb->expr()->neq('t.receiverStatus', '?2')
                )
            )
            ->setParameter(1, $receiver_id)
            ->setParameter(2, MessageBox::STATUS_RECEIVER_DELETED)
            ->orderBy('t.id', 'DESC')
            ->setMaxResults($size)->setFirstResult(($page - 1) * $size);

        return $qb->getQuery()->getResult();
    }


    /**
     * @param array $criteria
     * @param null $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getUniverseMessages($criteria = [], $order = null, $limit = 100, $offset = 0)
    {
        if (null == $order) {
            $order = [
                'created' => 'DESC'
            ];
        }
        return $this->entityManager->getRepository(MessageBox::class)->findBy($criteria, $order, $limit, $offset);
    }


    /**
     * Get a message content
     *
     * @param string $message_id
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
        $message->setId(Uuid::uuid1());
        $message->setTopic($topic);
        $message->setContent($content);
        $message->setStatus(MessageContent::STATUS_VALIDITY);
        $message->setCreated($dt);

        $this->saveModifiedEntity($message);
        $total = 0;
        $i = 0;

        foreach ($receiver as $target) {
            $entity = new MessageBox();
            $entity->setId(Uuid::uuid1());
            $entity->setMessageId($message->getId());
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