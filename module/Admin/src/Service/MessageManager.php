<?php
/**
 * MessageManager.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Service;


use Admin\Entity\Member;
use Admin\Entity\MessageBox;
use Admin\Entity\MessageContent;
use Application\Service\AppLogger;
use Doctrine\ORM\EntityManager;
use Ramsey\Uuid\Uuid;


class MessageManager extends BaseEntityManager
{

    /**
     * @var DepartmentManager
     */
    private $deptManager;

    /**
     * @var MemberManager
     */
    private $memberManager;



    public function __construct(
        MemberManager $memberManager,
        DepartmentManager $departmentManager,
        EntityManager $entityManager,
        AppLogger $logger)
    {
        parent::__construct($entityManager, $logger);

        $this->memberManager = $memberManager;
        $this->deptManager = $departmentManager;
    }


    /**
     * @param string $id
     *
     * @return MessageBox
     */
    public function getMessageBox($id)
    {
        if (empty($id)) {
            return null;
        }

        $qb = $this->resetQb();

        $qb->from(MessageBox::class, 't')->select('t');
        $qb->where($qb->expr()->eq('t.id', '?1'));
        $qb->setParameter(1, $id);

        return $this->getEntityFromPersistence();
    }


    /**
     * Get current member unread messages count
     *
     * @return int
     */
    public function getUnreadMessagesCount()
    {
        $member = $this->memberManager->getCurrentMember();
        if (!($member instanceof Member)) {
            return 0;
        }

        $qb = $this->resetQb();

        $qb->select($qb->expr()->count('t.id'));
        $qb->from(MessageBox::class, 't');

        $qb->where(
            $qb->expr()->andX(
                $qb->expr()->eq('t.receiver', '?1'),
                $qb->expr()->eq('t.receiverStatus', '?2')
            )
        );
        $qb->setParameter(1, $member->getMemberId())->setParameter(2, MessageBox::STATUS_RECEIVER_UNREAD);

        return $this->getEntitiesCount();
    }


    /**
     * Get current member latest messages
     *
     * @param int $count
     * @return array
     */
    public function getMyLatestMessages($count = 5)
    {
        $member = $this->memberManager->getCurrentMember();
        if (!($member instanceof Member)) {
            return [];
        }

        $qb = $this->resetQb();
        $qb->select('t')->from(MessageBox::class, 't');
        $qb->where(
            $qb->expr()->andX(
                $qb->expr()->eq('t.receiver', '?1'),
                $qb->expr()->neq('t.receiverStatus', '?2')
            )
        );
        $qb->setParameter(1, $member->getMemberId())->setParameter(2, MessageBox::STATUS_RECEIVER_DELETED);
        $qb->setMaxResults($count)->setFirstResult(0);
        $qb->orderBy('t.created', 'DESC');

        return $this->getEntitiesFromPersistence();
    }



    /**
     * Get my inbox messages count
     *
     * @return int
     */
    public function getInBoxMessagesCount()
    {
        $member = $this->memberManager->getCurrentMember();
        if (!($member instanceof Member)) {
            return 0;
        }

        $qb = $this->resetQb();

        $qb->select($qb->expr()->count('t.id'));
        $qb->from(MessageBox::class, 't');

        $qb->where(
            $qb->expr()->andX(
                $qb->expr()->eq('t.receiver', '?1'),
                $qb->expr()->neq('t.receiverStatus', '?2')
            )
        );
        $qb->setParameter(1, $member->getMemberId())->setParameter(2, MessageBox::STATUS_RECEIVER_DELETED);

        return $this->getEntitiesCount();
    }


    /**
     * Get my outbox messages count
     *
     * @return int
     */
    public function getOutBoxMessagesCount()
    {
        $member = $this->memberManager->getCurrentMember();
        if (!($member instanceof Member)) {
            return 0;
        }

        $qb = $this->resetQb();

        $qb->select($qb->expr()->count('t.id'));
        $qb->from(MessageBox::class, 't');

        $qb->where(
            $qb->expr()->andX(
                $qb->expr()->eq('t.sender', '?1'),
                $qb->expr()->neq('t.senderStatus', '?2')
            )
        );
        $qb->setParameter(1, $member->getMemberId())->setParameter(2, MessageBox::STATUS_SENDER_SENT);

        return $this->getEntitiesCount();
    }


    /**
     * Get my inbox messages
     *
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getInBoxMessagesByLimitPage($page = 1, $size = 10)
    {
        $member = $this->memberManager->getCurrentMember();
        if (!($member instanceof Member)) {
            return [];
        }

        $qb = $this->resetQb();
        $qb->select('t')->from(MessageBox::class, 't');
        $qb->where(
            $qb->expr()->andX(
                $qb->expr()->eq('t.receiver', '?1'),
                $qb->expr()->neq('t.receiverStatus', '?2')
            )
        );
        $qb->setParameter(1, $member->getMemberId())->setParameter(2, MessageBox::STATUS_RECEIVER_DELETED);
        $qb->setMaxResults($size)->setFirstResult(($page - 1) * $size);
        $qb->orderBy('t.created', 'DESC');

        return $this->getEntitiesFromPersistence();
    }


    /**
     * Get my outbox messages
     *
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getOutBoxMessagesByLimitPage($page = 1, $size = 10)
    {
        $member = $this->memberManager->getCurrentMember();
        if (!($member instanceof Member)) {
            return [];
        }

        $qb = $this->resetQb();
        $qb->select('t')->from(MessageBox::class, 't');
        $qb->where(
            $qb->expr()->andX(
                $qb->expr()->eq('t.sender', '?1'),
                $qb->expr()->neq('t.senderStatus', '?2')
            )
        );
        $qb->setParameter(1, $member->getMemberId())->setParameter(2, MessageBox::STATUS_SENDER_DELETED);
        $qb->setMaxResults($size)->setFirstResult(($page - 1) * $size);
        $qb->orderBy('t.created', 'DESC');

        return $this->getEntitiesFromPersistence();

    }


    /**
     * @param string $messageId
     * @return MessageContent
     */
    public function getMessageContent($messageId = null)
    {
        if (empty($messageId)) {
            return null;
        }

        $qb = $this->resetQb();

        $qb->from(MessageContent::class, 't')->select('t');
        $qb->where($qb->expr()->eq('t.id', '?1'));
        $qb->setParameter(1, $messageId);

        return $this->getEntityFromPersistence();
    }


    /**
     * Get all messages count
     *
     * @return int
     */
    public function getMessageContentsCount()
    {
        $qb = $this->resetQb();

        $qb->select($qb->expr()->count('t.id'));
        $qb->from(MessageContent::class, 't');

        $qb->where($qb->expr()->eq('t.status', '?1'));
        $qb->setParameter(1, MessageContent::STATUS_VALIDITY);

        return $this->getEntitiesCount();
    }


    /**
     * Get all messages
     *
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getMessageContentsByLimitPage($page = 1, $size = 10)
    {
        $qb = $this->resetQb();
        $qb->select('t')->from(MessageContent::class, 't');
        $qb->where($qb->expr()->eq('t.status', '?1'));
        $qb->setParameter(1, MessageContent::STATUS_VALIDITY);
        $qb->setMaxResults($size)->setFirstResult(($page - 1) * $size);
        $qb->orderBy('t.created', 'DESC');

        return $this->getEntitiesFromPersistence();
    }


    /**
     * Send multi messages
     *
     * @param array $receivers
     * @param string $topic
     * @param string $content
     * @return bool
     */
    public function sendMultiMessages($receivers = [], $topic = '', $content = '')
    {
        if (empty($topic) || empty($content)) {
            return false;
        }

        $sender = $this->memberManager->getCurrentMember();
        if (null == $sender) {
            return false;
        }

        if (empty($receivers)) {
            return false;
        }

        $dt = new \DateTime();

        $msg = new MessageContent();
        $msg->setId(Uuid::uuid1()->toString());
        $msg->setTopic($topic);
        $msg->setContent($content);
        $msg->setStatus(MessageContent::STATUS_VALIDITY);
        $msg->setCreated($dt);

        $entities = [];
        foreach ($receivers as $member) {
            if ($member instanceof Member) {

                if ($sender->getMemberId() == $member->getMemberId()) {
                    continue;
                }

                $entity = new MessageBox();
                $entity->setId(Uuid::uuid1()->toString());
                $entity->setContent($msg);
                $entity->setSender($sender->getMemberId());
                $entity->setSenderStatus(MessageBox::STATUS_SENDER_SENT);
                $entity->setSenderName($sender->getMemberName());
                $entity->setReceiver($member->getMemberId());
                $entity->setReceiverStatus(MessageBox::STATUS_RECEIVER_UNREAD);
                $entity->setReceiverName($member->getMemberName());
                $entity->setType(MessageBox::MESSAGE_TYPE_PERSONAL);
                $entity->setCreated($dt);
                array_push($entities, $entity);
            }
        }
        array_push($entities, $msg);

        $this->saveModifiedEntities($entities);

        return true;

    }


    /**
     * Send a message
     *
     * @param Member $receiver
     * @param string $topic
     * @param string $content
     * @return bool
     */
    public function sendOneMessage($receiver = null, $topic = '', $content = '')
    {
        if (null == $receiver || !($receiver instanceof Member)) {
            return false;
        }

        if (empty($topic) || empty($content)) {
            return false;
        }

        $member = $this->memberManager->getCurrentMember();
        if (null == $member) {
            return false;
        }

        if ($receiver->getMemberId() == $member->getMemberId()) {
            return false;
        }


        $dt = new \DateTime();

        $msg = new MessageContent();
        $msg->setId(Uuid::uuid1()->toString());
        $msg->setTopic($topic);
        $msg->setContent($content);
        $msg->setStatus(MessageContent::STATUS_VALIDITY);
        $msg->setCreated($dt);

        $box = new MessageBox();
        $box->setId(Uuid::uuid1()->toString());
        $box->setContent($msg);
        $box->setSender($member->getMemberId());
        $box->setSenderStatus(MessageBox::STATUS_SENDER_SENT);
        $box->setSenderName($member->getMemberName());
        $box->setReceiver($receiver->getMemberId());
        $box->setReceiverStatus(MessageBox::STATUS_RECEIVER_UNREAD);
        $box->setReceiverName($receiver->getMemberName());
        $box->setType(MessageBox::MESSAGE_TYPE_PERSONAL);
        $box->setCreated($dt);

        $this->saveModifiedEntities([$box, $msg]);

        return true;
    }



    /**
     * Broadcast a message
     *
     * @param string $topic
     * @param string $content
     * @param string $deptId
     * @return bool
     */
    public function broadcastMessage($topic = '', $content = '', $deptId = null)
    {
        if (empty($topic) || empty($content)) {
            return false;
        }

        $dt = new \DateTime();

        $msg = new MessageContent();
        $msg->setId(Uuid::uuid1()->toString());
        $msg->setTopic($topic);
        $msg->setContent($content);
        $msg->setStatus(MessageContent::STATUS_VALIDITY);
        $msg->setCreated($dt);

        $entities = [];

        $senderId = '-';
        $senderName = '系统';

        if (null === $deptId) {
            $dept = $this->deptManager->getDefaultDepartment();
        } else {
            $dept = $this->deptManager->getDepartment($deptId);
        }
        $members = $dept->getMembers();
        foreach ($members as $member) {
            if ($member instanceof Member) {

                $entity = new MessageBox();
                $entity->setId(Uuid::uuid1()->toString());
                $entity->setContent($msg);
                $entity->setSender($senderId);
                $entity->setSenderStatus(MessageBox::STATUS_SENDER_SENT);
                $entity->setSenderName($senderName); // Quick display
                $entity->setReceiver($member->getMemberId());
                $entity->setReceiverStatus(MessageBox::STATUS_RECEIVER_UNREAD);
                $entity->setReceiverName($member->getMemberName()); // Quick display
                $entity->setType(MessageBox::MESSAGE_TYPE_BROADCAST);
                $entity->setCreated($dt);
                array_push($entities, $entity);
            }
        }
        array_push($entities, $msg);

        $this->saveModifiedEntities($entities);

        return true;
    }
}