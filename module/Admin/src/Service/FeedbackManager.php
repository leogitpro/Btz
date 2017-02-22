<?php
/**
 * FeedbackManager.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Service;


use Admin\Entity\Feedback;
use Admin\Entity\Member;

class FeedbackManager extends BaseEntityManager
{

    /**
     * Query a member feedback count
     *
     * @param Member $member
     * @return int
     */
    public function getMemberFeedbackCount($member)
    {
        $qb = $this->resetQb();
        $qb->select($qb->expr()->count('t.id'));
        $qb->from(Feedback::class, 't');

        $qb->where($qb->expr()->eq('t.sender', '?1'));
        $qb->setParameter(1, $member);

        return $this->getEntitiesCount();
    }


    /**
     * Query system all feedback count for supper administrator
     *
     * @return int
     */
    public function getAllFeedbackCount()
    {
        $qb = $this->resetQb();
        $qb->select($qb->expr()->count('t.id'));
        $qb->from(Feedback::class, 't');

        return $this->getEntitiesCount();
    }


    /**
     * Query member feedback list
     *
     * @param Member $member
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getMemberFeedbackByLimitPage($member, $page = 1, $size = 10)
    {
        $qb = $this->resetQb();
        $qb->select('t')->from(Feedback::class, 't');
        $qb->where($qb->expr()->eq('t.sender', '?1'));
        $qb->setParameter(1, $member);
        $qb->setMaxResults($size)->setFirstResult(($page - 1) * $size);
        $qb->orderBy('t.created', 'DESC');

        return $this->getEntitiesFromPersistence();
    }


    /**
     * Query feedback list for supper administrator
     *
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getFeedbackByLimitPage($page = 1, $size = 10)
    {
        $qb = $this->resetQb();
        $qb->select('t')->from(Feedback::class, 't');
        $qb->setMaxResults($size)->setFirstResult(($page - 1) * $size);
        $qb->orderBy('t.updated', 'ASC');

        return $this->getEntitiesFromPersistence();
    }

}