<?php
/**
 * WeChatClientManager.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Service;


use Admin\Entity\WeChat;
use Admin\Entity\WeChatClient;
use Ramsey\Uuid\Uuid;


class WeChatClientManager extends BaseEntityManager
{

    /**
     * @param string $clientId
     * @return WeChatClient
     */
    public function getWeChatClient($clientId)
    {
        $qb = $this->resetQb();

        $qb->select('t')->from(WeChatClient::class, 't');
        $qb->where($qb->expr()->eq('t.id', '?1'));
        $qb->setParameter(1, $clientId);

        return $this->getEntityFromPersistence();
    }


    /**
     * @param WeChat $weChat
     * @return int
     */
    public function getClientCountByWeChat($weChat)
    {
        $qb = $this->resetQb();

        $qb->select($qb->expr()->count('t.id'));
        $qb->from(WeChatClient::class, 't');

        $qb->where($qb->expr()->eq('t.weChat', '?1'));
        $qb->setParameter(1, $weChat);

        return $this->getEntitiesCount();
    }


    /**
     * @param WeChat $weChat
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getClientsWithLimitPageByWeChat($weChat, $page = 1, $size = 10)
    {
        $qb = $this->resetQb();

        $qb->select('t')->from(WeChatClient::class, 't');

        $qb->where($qb->expr()->eq('t.weChat', '?1'));
        $qb->setParameter(1, $weChat);

        $qb->setMaxResults($size)->setFirstResult(($page -1) * $size);

        $qb->orderBy('t.expireTime', 'DESC');

        return $this->getEntitiesFromPersistence();
    }



    /**
     * @param WeChat $weChat
     * @param string $name
     * @param string $domain
     * @param string $ip
     * @param integer $start
     * @param integer $end
     */
    public function createWeChatClient($weChat, $name, $domain, $ip, $start, $end)
    {
        $client = new WeChatClient();
        $client->setId(Uuid::uuid1()->toString());
        $client->setName($name);
        $client->setDomain($domain);
        $client->setIp($ip);
        $client->setActiveTime($start);
        $client->setExpireTime($end);
        $client->setCreated(new \DateTime());
        $client->setWeChat($weChat);

        $this->saveModifiedEntity($client);
    }



}