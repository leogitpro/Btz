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

class MemberManager
{

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Logger
     */
    private $logger;


    public function __construct(EntityManager $entityManager, Logger $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
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
        //if ($member instanceof Member) {
        $this->entityManager->persist($member);
        $this->entityManager->flush();
        //}

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



}