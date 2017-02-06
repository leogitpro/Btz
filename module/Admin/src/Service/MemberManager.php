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
     * @var DMRelationManager
     */
    private $dmrManager;

    /**
     * @var AuthService
     */
    private $authService;


    /**
     * @var Member
     */
    private $__member = null;


    public function __construct(AuthService $authService,  DMRelationManager $dmrManager, EntityManager $entityManager, Logger $logger)
    {
        parent::__construct($entityManager, $logger);

        $this->authService = $authService;
        $this->dmrManager = $dmrManager;
        $this->__member = null;
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

        if (null === $this->__member) {
            $identity = $this->authService->getIdentity();
            $this->__member = $this->getMember($identity);
        }

        return $this->__member;
    }


    /**
     * Get all members count
     *
     * @return integer
     */
    public function getAllMembersCount()
    {
        return $this->getEntitiesCount(Member::class, 'member_id');
    }


    /**
     * Get all members, max records: 200
     *
     * @return array
     */
    public function getAllMembers()
    {
        return $this->getUniverseMembers([], null, 200);
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
        return $this->getUniverseMembers([], null, $size, ($page - 1) * $size);
    }


    /**
     * Get activated members count
     */
    public function getMembersCount()
    {
        return $this->getEntitiesCount(Member::class, 'member_id', ['member_status = :status'], ['status' => Member::STATUS_ACTIVATED]);
    }


    /**
     * Get activated members, mas records: 200
     *
     * @return array
     */
    public function getMembers()
    {
        return $this->getUniverseMembers(['member_status' => Member::STATUS_ACTIVATED], null, 200);
    }


    /**
     * Get activated members by page
     *
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getMembersByLimitPage($page = 1, $size = 10)
    {
        return $this->getUniverseMembers(['member_status' => Member::STATUS_ACTIVATED], null, $size, ($page - 1) * $size);
    }


    /**
     * Get members from repository
     *
     * @param array $criteria
     * @param null|array $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    private function getUniverseMembers($criteria, $order = null, $limit = 10, $offset = 0)
    {
        if (null == $order) {
            $order = [
                'member_status' => 'ASC',
                'member_level' => 'DESC',
                'member_name' => 'ASC',
            ];
        }
        return $this->entityManager->getRepository(Member::class)->findBy($criteria, $order, $limit, $offset);
    }


    /**
     * Get administrator information
     *
     * @param integer $member_id
     * @return Member
     */
    public function getMember($member_id)
    {
        return $this->getUniverseMember(['member_id' => $member_id]);
    }


    /**
     * Get administrator information by email
     *
     * @param string $email
     * @return Member
     */
    public function getMemberByEmail($email)
    {
        return $this->getUniverseMember(['member_email' => $email]);
    }


    /**
     * Get a member from repository
     *
     * @param array $criteria
     * @param null|array $order
     * @return null|object
     */
    private function getUniverseMember($criteria, $order = null)
    {
        return $this->entityManager->getRepository(Member::class)->findOneBy($criteria, $order);
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

            $this->dmrManager->memberToBeInvalid($member->getMemberId());

            $member->setMemberStatus(Member::STATUS_RETRIED);
            $member = $this->saveModifiedEntity($member);

        } else { // to be activated, only restore with default department relation

            $member->setMemberStatus(Member::STATUS_ACTIVATED);
            $member = $this->saveModifiedEntity($member);

            $this->dmrManager->memberBeActivated($member->getMemberId());

        }

        return $member;
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
        $member->setMemberStatus(Member::STATUS_RETRIED);
        $member->setMemberLevel(Member::LEVEL_INTERIOR);
        $member->setMemberCreated(new \DateTime());

        $member = $this->saveModifiedEntity($member);

        $this->dmrManager->initNewMemberCreated($member->getMemberId());

        return $member;
    }

}