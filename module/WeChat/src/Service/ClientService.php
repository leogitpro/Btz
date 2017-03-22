<?php
/**
 * ClientService.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace WeChat\Service;


use Ramsey\Uuid\Uuid;
use WeChat\Entity\Account;
use WeChat\Entity\Client;


class ClientService extends BaseEntityService
{

    /**
     * @param string $clientId
     * @return Client
     */
    public function getWeChatClient($clientId)
    {
        $qb = $this->resetQb();

        $qb->select('t')->from(Client::class, 't');
        $qb->where($qb->expr()->eq('t.id', '?1'));
        $qb->setParameter(1, $clientId);

        return $this->getEntityFromPersistence();
    }


    /**
     * @param Account $weChat
     * @return int
     */
    public function getClientCountByWeChat($weChat)
    {
        $qb = $this->resetQb();

        $qb->select($qb->expr()->count('t.id'));
        $qb->from(Client::class, 't');

        $qb->where($qb->expr()->eq('t.weChat', '?1'));
        $qb->setParameter(1, $weChat);

        return $this->getEntitiesCount();
    }


    /**
     * @param Account $weChat
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getClientsWithLimitPageByWeChat($weChat, $page = 1, $size = 10)
    {
        $qb = $this->resetQb();

        $qb->select('t')->from(Client::class, 't');

        $qb->where($qb->expr()->eq('t.weChat', '?1'));
        $qb->setParameter(1, $weChat);

        $qb->setMaxResults($size)->setFirstResult(($page -1) * $size);

        $qb->orderBy('t.expireTime', 'DESC');

        return $this->getEntitiesFromPersistence();
    }


    /**
     * @param Account $weChat
     * @param string $name
     * @param string $domain
     * @param string $ip
     * @param integer $start
     * @param integer $end
     */
    public function createWeChatClient($weChat, $name, $domain, $ip, $start, $end)
    {
        $client = new Client();
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