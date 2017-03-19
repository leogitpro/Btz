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
use Logger\Service\LoggerService;
use Ramsey\Uuid\Uuid;


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


    public function __construct(AuthService $authService, EntityManager $entityManager, LoggerService $logger)
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
        $qb = $this->resetQb();

        $qb->select($qb->expr()->count('t.memberId'));
        $qb->from(Member::class, 't');

        return $this->getEntitiesCount();
    }


    /**
     * Get activated members count
     *
     * @return int
     */
    public function getMembersCount()
    {
        $qb = $this->resetQb();

        $qb->select($qb->expr()->count('t.memberId'));
        $qb->from(Member::class, 't');

        $qb->where($qb->expr()->eq('t.memberStatus', '?1'));
        $qb->setParameter(1, Member::STATUS_ACTIVATED);

        return $this->getEntitiesCount();
    }


    /**
     * Get administrator information
     *
     * @param string $member_id
     * @return Member
     * @throws \Exception
     */
    public function getMember($member_id = null)
    {
        if (empty($member_id)) {
            throw new \Exception('没有成员的编号, 我们无法为您查询信息哦!');
        }

        $qb = $this->resetQb();

        $qb->from(Member::class, 't')->select('t');
        $qb->where($qb->expr()->eq('t.memberId', '?1'));
        $qb->setParameter(1, $member_id);

        $obj = $this->getEntityFromPersistence();
        if (!$obj instanceof Member) {
            throw new \Exception('这个成员编号失效了哦!');
        }
        return $obj;
    }


    /**
     * @return Member
     * @throws \Exception
     */
    public function getCurrentMember()
    {
        if(!$this->authService->hasIdentity()) {
            throw new \Exception('您需要重新登录一下更新状态哦!');
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
        $qb = $this->resetQb();

        $qb->from(Member::class, 't')->select('t');
        $qb->where($qb->expr()->eq('t.memberEmail', '?1'));
        $qb->setParameter(1, $email);

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
        $qb = $this->resetQb();

        $qb->select('t')->from(Member::class, 't');

        $qb->where($qb->expr()->eq('t.memberStatus', '?1'));
        $qb->setParameter(1, Member::STATUS_ACTIVATED);

        $qb->setMaxResults($size)->setFirstResult(($page -1) * $size);

        $qb->orderBy('t.memberStatus')->addOrderBy('t.memberLevel', 'DESC')->addOrderBy('t.memberName');

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
     * Search member by name.
     *
     * @param string $key
     * @return array
     */
    public function getMembersBySearch($key = null)
    {
        if (empty($key)) {
            return [];
        }

        $qb = $this->resetQb();
        $qb->select('t')->from(Member::class, 't');

        $qb->where(
            $qb->expr()->andX(
                $qb->expr()->eq('t.memberStatus', '?1'),
                $qb->expr()->like('t.memberName', '?2')
            )
        );
        $qb->setParameter(1, Member::STATUS_ACTIVATED);
        $qb->setParameter(2, '%' . $key . '%');

        $qb->setMaxResults(10)->setFirstResult(0);

        $qb->orderBy('t.memberStatus')->addOrderBy('t.memberLevel', 'DESC')->addOrderBy('t.memberName');

        return $this->getEntitiesFromPersistence();
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
        $qb = $this->resetQb();

        $qb->select('t')->from(Member::class, 't');

        $qb->setMaxResults($size)->setFirstResult(($page -1) * $size);

        $qb->orderBy('t.memberStatus')->addOrderBy('t.memberLevel', 'DESC')->addOrderBy('t.memberName');

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


    /**
     * Create a member
     *
     * @param string $email
     * @param string $password
     * @param string $name
     * @param array $depts
     * @return Member
     */
    public function createMember($email, $password, $name, $depts = [])
    {
        $member = new Member();
        $member->setMemberId(Uuid::uuid1()->toString());
        $member->setMemberEmail($email);
        $member->setMemberPassword($password);
        $member->setMemberName($name);
        $member->setMemberStatus(Member::STATUS_ACTIVATED);
        $member->setMemberLevel(Member::LEVEL_INTERIOR);
        $member->setMemberExpired(new \DateTime("last day"));
        $member->setMemberCreated(new \DateTime());

        foreach ($depts as $dept) {
            $member->getDepts()->add($dept);
        }

        $this->saveModifiedEntity($member);

        return $member;
    }
}