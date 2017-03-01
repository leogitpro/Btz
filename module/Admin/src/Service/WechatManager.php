<?php
/**
 * WeChatManager.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Service;


use Admin\Entity\Member;
use Admin\Entity\WeChat;


class WeChatManager extends BaseEntityManager
{

    /**
     * @param $weChatId
     * @return WeChat
     */
    public function getWeChat($weChatId)
    {
        $qb = $this->resetQb();

        $qb->select('t')->from(WeChat::class, 't');
        $qb->where($qb->expr()->eq('t.wxId', '?1'));
        $qb->setParameter(1, $weChatId);

        return $this->getEntityFromPersistence();
    }


    /**
     * @param string $appId
     * @return WeChat
     */
    public function getWeChatByAppId($appId)
    {
        $qb = $this->resetQb();

        $qb->from(WeChat::class, 't')->select('t');
        $qb->where($qb->expr()->eq('t.wxAppId', '?1'));
        $qb->setParameter(1, $appId);

        return $this->getEntityFromPersistence();
    }


    /**
     * @param string $appId
     * @return int
     */
    public function getWeChatCountByAppId($appId)
    {
        $qb = $this->resetQb();

        $qb->select($qb->expr()->count('t.wxId'));
        $qb->from(WeChat::class, 't');
        $qb->where($qb->expr()->eq('t.wxAppId', '?1'));
        $qb->setParameter(1, $appId);

        return $this->getEntitiesCount();
    }


    /**
     * @param Member $member
     * @return WeChat
     */
    public function getWeChatByMember($member)
    {
        $qb = $this->resetQb();

        $qb->from(WeChat::class, 't')->select('t');
        $qb->where($qb->expr()->eq('t.member', '?1'));
        $qb->setParameter(1, $member);

        return $this->getEntityFromPersistence();
    }



    /**
     * @param Member $member
     * @param string $appId
     * @param string $appSecret
     */
    public function createMemberWeChat($member, $appId, $appSecret)
    {
        $weChat = new WeChat();
        $weChat->setWxAppId($appId);
        $weChat->setWxAppSecret($appSecret);
        $weChat->setWxChecked(WeChat::STATUS_UNCHECK);
        $weChat->setWxExpired(strtotime("+7 days"));
        $weChat->setWxCreated(new \DateTime());
        $weChat->setMember($member);

        $this->saveModifiedEntity($weChat);
    }


}