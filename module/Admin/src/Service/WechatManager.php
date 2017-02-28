<?php
/**
 * WechatManager.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Service;


use Admin\Entity\Member;
use Admin\Entity\Wechat;
use Admin\Entity\WechatClient;
use Admin\Entity\WechatQrcode;
use Ramsey\Uuid\Uuid;

class WechatManager extends BaseEntityManager
{

    /**
     * Get a wechat information
     *
     * @param integer $wechatId
     * @return Wechat
     */
    public function getWechat($wechatId)
    {
        $qb = $this->resetQb();

        $qb->select('t')->from(Wechat::class, 't');
        $qb->where($qb->expr()->eq('t.wxId', '?1'));
        $qb->setParameter(1, $wechatId);

        return $this->getEntityFromPersistence();
    }


    /**
     * @param string $clientId
     * @return WechatClient
     */
    public function getWechatClient($clientId)
    {
        $qb = $this->resetQb();

        $qb->select('t')->from(WechatClient::class, 't');
        $qb->where($qb->expr()->eq('t.id', '?1'));
        $qb->setParameter(1, $clientId);

        return $this->getEntityFromPersistence();
    }


    /**
     * @param string $appId
     * @return Wechat
     */
    public function getWechatByAppId($appId)
    {
        $qb = $this->resetQb();

        $qb->from(Wechat::class, 't')->select('t');
        $qb->where($qb->expr()->eq('t.wxAppId', '?1'));
        $qb->setParameter(1, $appId);

        return $this->getEntityFromPersistence();
    }


    /**
     * @param string $appId
     * @return int
     */
    public function getWechatCountByAppId($appId)
    {
        $qb = $this->resetQb();

        $qb->select($qb->expr()->count('t.wxId'));
        $qb->from(Wechat::class, 't');
        $qb->where($qb->expr()->eq('t.wxAppId', '?1'));
        $qb->setParameter(1, $appId);

        return $this->getEntitiesCount();
    }



    /**
     * @param Member $member
     * @return Wechat
     */
    public function getWechatByMember($member)
    {
        $qb = $this->resetQb();

        $qb->from(Wechat::class, 't')->select('t');
        $qb->where($qb->expr()->eq('t.member', '?1'));
        $qb->setParameter(1, $member);

        return $this->getEntityFromPersistence();
    }






    /**
     * @param Member $member
     * @param string $appid
     * @param string $appsecret
     */
    public function createMemberWechat($member, $appid, $appsecret)
    {
        $wechat = new Wechat();
        $wechat->setWxAppId($appid);
        $wechat->setWxAppSecret($appsecret);
        $wechat->setWxChecked(Wechat::STATUS_UNCHECK);
        $wechat->setWxExpired(strtotime("+7 days"));
        $wechat->setWxCreated(new \DateTime());
        $wechat->setMember($member);

        $this->saveModifiedEntity($wechat);
    }


    /**
     * @param WechatClient $wechat
     * @param string $name
     * @param string $domain
     * @param string $ip
     * @param integer $start
     * @param integer $end
     */
    public function createWechatClient($wechat, $name, $domain, $ip, $start, $end)
    {
        $client = new WechatClient();
        $client->setId(Uuid::uuid1()->toString());
        $client->setName($name);
        $client->setDomain($domain);
        $client->setIp($ip);
        $client->setActiveTime($start);
        $client->setExpireTime($end);
        $client->setCreated(new \DateTime());
        $client->setWechat($wechat);

        $this->saveModifiedEntity($client);
    }


    /**
     * @param Wechat $wechat
     * @param string $name
     * @param string $type
     * @param integer $expired
     * @param string $scene
     * @param string $url
     */
    public function createWechatQrcode($wechat, $name, $type, $expired, $scene, $url)
    {
        $qrcode = new WechatQrcode();
        $qrcode->setId(Uuid::uuid1()->toString());
        $qrcode->setName($name);
        $qrcode->setType($type);
        $qrcode->setExpired((time() + $expired));
        $qrcode->setScene($scene);
        $qrcode->setUrl($url);
        $qrcode->setCreated(new \DateTime());
        $qrcode->setWechat($wechat);

        $this->saveModifiedEntity($qrcode);
    }

}