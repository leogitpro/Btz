<?php
/**
 * AccountService.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace WeChat\Service;


use Admin\Entity\Member;
use WeChat\Entity\Account;
use WeChat\Exception\InvalidArgumentException;
use WeChat\Exception\RuntimeException;


class AccountService extends BaseEntityService
{

    /**
     * 查询微信公众号个数
     *
     * @return int
     */
    public function getWeChatCount()
    {
        $qb = $this->resetQb();

        $qb->select($qb->expr()->count('t.wxId'));
        $qb->from(Account::class, 't');

        return $this->getEntitiesCount();
    }


    /**
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getWeChatLimitByPage($page = 1, $size = 10)
    {
        $qb = $this->resetQb();

        $qb->select('t')->from(Account::class, 't');
        $qb->setMaxResults($size)->setFirstResult(($page -1) * $size);
        $qb->orderBy('t.wxExpired', 'DESC');

        return $this->getEntitiesFromPersistence();
    }


    /**
     * @param int $weChatId
     * @param bool $ignoreExpired
     * @return Account
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function getWeChat($weChatId, $ignoreExpired = false)
    {
        $qb = $this->resetQb();

        $qb->select('t')->from(Account::class, 't');
        $qb->where($qb->expr()->eq('t.wxId', '?1'));
        $qb->setParameter(1, $weChatId);

        $weChat = $this->getEntityFromPersistence();
        if (!$weChat instanceof Account) {
            throw new InvalidArgumentException('无此编号的微信公众号帐号信息: ' . $weChatId);
        }

        if (!$ignoreExpired) {
            if ($weChat->getWxExpired() < time()) {
                throw new RuntimeException('公众号已过期!');
            }
        }

        return $weChat;
    }


    /**
     * @param string $appId
     * @param bool $ignoreExpired
     * @return Account
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function getWeChatByAppId($appId, $ignoreExpired = false)
    {
        $qb = $this->resetQb();

        $qb->from(Account::class, 't')->select('t');
        $qb->where($qb->expr()->eq('t.wxAppId', '?1'));
        $qb->setParameter(1, $appId);

        $weChat = $this->getEntityFromPersistence();
        if (!$weChat instanceof Account) {
            throw new InvalidArgumentException('无此AppID的微信公众号帐号信息: ' . $appId);
        }

        if (!$ignoreExpired) {
            if ($weChat->getWxExpired() < time()) {
                throw new RuntimeException('公众号已过期!');
            }
        }

        return $weChat;
    }


    /**
     * @param string $appId
     * @return int
     */
    public function getWeChatCountByAppId($appId)
    {
        $qb = $this->resetQb();

        $qb->select($qb->expr()->count('t.wxId'));
        $qb->from(Account::class, 't');
        $qb->where($qb->expr()->eq('t.wxAppId', '?1'));
        $qb->setParameter(1, $appId);

        return $this->getEntitiesCount();
    }


    /**
     * @param Member $member
     * @param bool $ignoreExpired
     * @return Account
     * @throws InvalidArgumentException
     */
    public function getWeChatByMember($member, $ignoreExpired = false)
    {
        $qb = $this->resetQb();

        $qb->from(Account::class, 't')->select('t');
        $qb->where($qb->expr()->eq('t.member', '?1'));
        $qb->setParameter(1, $member);

        $weChat = $this->getEntityFromPersistence();
        if (!$weChat instanceof Account) {
            throw new InvalidArgumentException('该用户无微信公众号帐号信息');
        }

        if (!$ignoreExpired) {
            if ($weChat->getWxExpired() < time()) {
                throw new RuntimeException('公众号已过期!');
            }
        }

        return $weChat;
    }


    /**
     * @param Member $member
     * @param string $appId
     * @param string $appSecret
     */
    public function createMemberWeChat($member, $appId, $appSecret, $accessToken, $expired)
    {
        $weChat = new Account();
        $weChat->setWxAppId($appId);
        $weChat->setWxAppSecret($appSecret);
        $weChat->setWxChecked(Account::STATUS_CHECKED);
        $weChat->setWxAccessToken($accessToken);
        $weChat->setWxAccessTokenExpired($expired);
        $weChat->setWxExpired(strtotime("+7 days"));
        $weChat->setWxCreated(new \DateTime());
        $weChat->setMember($member);

        $this->saveModifiedEntity($weChat);
    }


}