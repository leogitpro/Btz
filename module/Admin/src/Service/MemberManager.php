<?php
/**
 * MemberManager.php
 *
 * Admin module member manager
 *
 * @author: leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Service;


use Admin\Entity\Member;
use Doctrine\ORM\EntityManager;
use Zend\Log\Logger;


class MemberManager extends BaseEntityManager
{

    /**
     * @var AuthService
     */
    private $authService;


    /**
     * @var Member
     */
    private $currentMember = null;


    public function __construct(AuthService $authService, EntityManager $entityManager, Logger $logger)
    {
        parent::__construct($entityManager, $logger);

        $this->authService = $authService;

        $this->currentMember = null;
    }


    /**
     * Get all members count
     *
     * @return integer
     */
    public function getAllMembersCount()
    {
        $this->resetQb();

        $this->getQb()->select($this->getQb()->expr()->count('t.memberId'));
        $this->getQb()->from(Member::class, 't');

        return $this->getEntitiesCount();
    }


    /**
     * Get activated members count
     *
     * @return int
     */
    public function getMembersCount()
    {
        $this->resetQb();

        $this->getQb()->select($this->getQb()->expr()->count('t.memberId'));
        $this->getQb()->from(Member::class, 't');

        $this->getQb()->where($this->getQb()->expr()->eq('t.memberStatus', '?1'));
        $this->getQb()->setParameter(1, Member::STATUS_ACTIVATED);

        return $this->getEntitiesCount();
    }


    /**
     * Get administrator information
     *
     * @param string $member_id
     * @return Member
     */
    public function getMember($member_id)
    {
        $this->resetQb();

        $this->getQb()->from(Member::class, 't')->select('t');
        $this->getQb()->where($this->getQb()->expr()->eq('t.memberId', '?1'));
        $this->getQb()->setParameter(1, $member_id);

        return $this->getEntityFromPersistence();
    }


    /**
     * Get current member
     *
     * @return Member|null
     */
    public function getCurrentMember()
    {
        if(!$this->authService->hasIdentity()) {
            return null;
        }

        if (null === $this->currentMember) {
            $identity = $this->authService->getIdentity();
            $this->currentMember = $this->getMember($identity);
        }

        return $this->currentMember;
    }


    /**
     * Get administrator information by email
     *
     * @param string $email
     * @return Member
     */
    public function getMemberByEmail($email)
    {
        $this->resetQb();

        $this->getQb()->from(Member::class, 't')->select('t');
        $this->getQb()->where($this->getQb()->expr()->eq('t.memberEmail', '?1'));
        $this->getQb()->setParameter(1, $email);

        return $this->getEntityFromPersistence();
    }


    /**
     * Get activated members by page
     *
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getMembersByLimitPage($page = 1, $size = 100)
    {
        $this->resetQb();

        $this->getQb()->select('t')->from(Member::class, 't');

        $this->getQb()->where($this->getQb()->expr()->eq('t.memberStatus', '?1'));
        $this->getQb()->setParameter(1, Member::STATUS_ACTIVATED);

        $this->getQb()->setMaxResults($size)->setFirstResult(($page -1) * $size);

        $this->getQb()->orderBy('t.memberStatus')->addOrderBy('t.memberLevel', 'DESC')->addOrderBy('t.memberName');

        return $this->getEntitiesFromPersistence();
    }


    /**
     * Get activated members, mas records: 200
     *
     * @return array
     */
    public function getMembers()
    {
        return $this->getMembersByLimitPage(1, 200);
    }


    /**
     * Get all members by limit page
     *
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getAllMembersByLimitPage($page = 1, $size = 100)
    {
        $this->resetQb();

        $this->getQb()->select('t')->from(Member::class, 't');

        $this->getQb()->setMaxResults($size)->setFirstResult(($page -1) * $size);

        $this->getQb()->orderBy('t.memberStatus')->addOrderBy('t.memberLevel', 'DESC')->addOrderBy('t.memberName');

        return $this->getEntitiesFromPersistence();
    }


    /**
     * Get all members, max records: 200
     *
     * @return array
     */
    public function getAllMembers()
    {
        return $this->getAllMembersByLimitPage(1, 200);
    }
}