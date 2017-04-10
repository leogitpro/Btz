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
use Admin\Exception\InvalidArgumentException;
use Admin\Exception\RuntimeException;
use Doctrine\ORM\EntityManager;
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


    public function __construct(AuthService $authService, EntityManager $entityManager)
    {
        parent::__construct($entityManager);

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
     * @param bool $validate
     * @return Member
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function getMember($member_id = null, $validate = true)
    {
        if (empty($member_id)) {
            throw new InvalidArgumentException('不能查询空的成员编号');
        }

        $qb = $this->resetQb();

        $qb->from(Member::class, 't')->select('t');
        $qb->where($qb->expr()->eq('t.memberId', '?1'));
        $qb->setParameter(1, $member_id);

        $obj = $this->getEntityFromPersistence();
        if (!$obj instanceof Member) {
            throw new InvalidArgumentException('无效的成员编号');
        }

        if (!$validate) {
            return $obj;
        }

        $expired = $obj->getMemberExpired();
        if(time() > $expired->format('U')) {
            throw new RuntimeException('用户账号已经过期');
        }

        if (Member::STATUS_ACTIVATED != $obj->getMemberStatus()) {
            throw new RuntimeException('用户账号已经被锁定');
        }

        return $obj;
    }


    /**
     * @return Member
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function getCurrentMember()
    {
        if(!$this->authService->hasIdentity()) {
            throw new RuntimeException('您需要重新登录一下更新状态哦!');
        }

        if (null === $this->currentMember) {
            $identity = $this->authService->getIdentity();
            $this->currentMember = $this->getMember($identity);
        }

        return $this->currentMember;
    }


    /**
     * @param $activeCode
     * @return Member
     * @throws InvalidArgumentException
     */
    public function getMemberByActiveCode($activeCode)
    {
        if(empty($activeCode)) {
            throw new InvalidArgumentException('激活码不能为空');
        }

        $qb = $this->resetQb();

        $qb->from(Member::class, 't')->select('t');
        $qb->where($qb->expr()->eq('t.memberActiveCode', '?1'));
        $qb->setParameter(1, $activeCode);

        $obj = $this->getEntityFromPersistence();
        if (!$obj instanceof Member) {
            throw new InvalidArgumentException('无效的账号激活码');
        }
        return $obj;
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
        $member->setMemberExpired(new \DateTime("next year"));
        $member->setMemberCreated(new \DateTime());

        foreach ($depts as $dept) {
            $member->getDepts()->add($dept);
        }

        $this->saveModifiedEntity($member);

        return $member;
    }

}