<?php
/**
 * OauthService.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace WeChat\Service;


use WeChat\Entity\Oauth;
use WeChat\Exception\InvalidArgumentException;


class OauthService extends BaseEntityService
{

    /**
     * @param string $id
     * @return Oauth
     * @throws InvalidArgumentException
     */
    public function getOauthUrl($id)
    {
        $qb = $this->resetQb();

        $qb->select('t');
        $qb->from(Oauth::class, 't');

        $qb->where($qb->expr()->eq('t.id', '?1'));
        $qb->setParameter(1, $id);

        $obj = $this->getEntityFromPersistence();
        if (!$obj instanceof Oauth) {
            throw new InvalidArgumentException('Invalid oauth url id');
        }
        return $obj;
    }


    /**
     * @param string $url
     * @return Oauth
     * @throws InvalidArgumentException
     */
    public function saveOauthUrl($url)
    {
        if (empty($url)) {
            throw new InvalidArgumentException('Url is empty');
        }

        $hash = md5($url);

        try {
            return $this->getOauthUrl($hash);
        } catch (InvalidArgumentException $e) {
            //todo
        }

        $entity = new Oauth();
        $entity->setId($hash);
        $entity->setUrl($url);
        $entity->setCreated(new \DateTime());

        $this->saveModifiedEntity($entity);
        return $entity;
    }



}