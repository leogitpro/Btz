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
     * @var DepartmentMemberRelationManager
     */
    private $dmRelationManager;


    public function __construct(
        EntityManager $entityManager,
        Logger $logger,
        DepartmentMemberRelationManager $departmentMemberRelationManager
    )
    {
        $this->dmRelationManager = $departmentMemberRelationManager;

        parent::__construct($entityManager, $logger);
    }


    /**
     * Get all members count
     *
     * @return integer
     */
    public function getAllMembersCount()
    {
        $qb = $this->entityManager->getRepository(Member::class)->createQueryBuilder('t');
        return $qb->select('count(t.member_id)')->getQuery()->getSingleScalarResult();
    }


    /**
     * Get all members
     *
     * @return array
     */
    public function getAllMembers()
    {
        return $this->entityManager->getRepository(Member::class)->findAll();
    }


    /**
     * Get all members by limit page
     *
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getAllMembersByLimitPage($page = 1, $size = 10)
    {
        return $this->entityManager->getRepository(Member::class)->findBy([], ['member_id' => 'ASC',], $size, ($page - 1) * $size);
    }


    /**
     * Get activated members
     *
     * @return array
     */
    public function getMembers()
    {
        return $this->entityManager->getRepository(Member::class)->findBy([
            'member_status' => Member::STATUS_ACTIVATED
        ]);
    }


    /**
     * Get administrator information
     *
     * @param integer $member_id
     * @return Member
     */
    public function getMember($member_id)
    {
        return $this->entityManager->getRepository(Member::class)->find($member_id);
    }


    /**
     * Get administrator information by email
     *
     * @param string $email
     * @return Member
     */
    public function getMemberByEmail($email)
    {
        return $this->entityManager->getRepository(Member::class)->findOneBy(['member_email' => $email]);
    }


    /**
     * Save modified Member instance
     *
     * @param Member $member
     * @return Member
     */
    public function saveModifiedMember(Member $member)
    {
        return $this->saveModifiedEntity($member);
    }


    /**
     * Update member status
     *
     * @param Member $member
     * @param integer $status
     * @return Member
     */
    public function updateMemberStatus(Member $member, $status)
    {
        $oldStatus = $member->getMemberStatus();
        if ($oldStatus == $status) {
            return $member;
        }

        if ($oldStatus == Member::STATUS_ACTIVATED) { // to be retried
            $this->dmRelationManager->closedOneMember($member->getMemberId());
        } else { // to be activated, only restore with default department relation
            $this->dmRelationManager->openedOneMember($member->getMemberId());
        }

        $member->setMemberStatus($status);

        return $this->saveModifiedMember($member);
    }


    /**
     * Update member password
     *
     * @param integer $member_id
     * @param string $password MD5 value
     * @return Member
     */
    public function updateMemberPassword($member_id, $password)
    {
        $member = $this->getMember($member_id);
        if (null == $member) {
            $this->logger->err(__METHOD__ . PHP_EOL . 'Get member by id(' . $member_id . ') failure');
            return false;
        }

        $member->setMemberPassword($password);

        return $this->saveModifiedMember($member);
    }


    /**
     * Create new member
     *
     * @param array $data
     * @return Member
     */
    public function createMember($data)
    {
        $member = new Member();

        $member->setMemberEmail($data['email']);
        $member->setMemberPassword($data['password']);
        $member->setMemberName($data['name']);
        //$member->setMemberStatus($data['status']);
        //$member->setMemberLevel($data['level']);
        $member->setMemberCreated(new \DateTime());

        $this->entityManager->persist($member);
        $this->entityManager->flush();

        $this->dmRelationManager->initNewMemberDepartments($member->getMemberId());

        return $member;
    }

}