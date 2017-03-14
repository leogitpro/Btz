<?php
/**
 * WeChatTagManager.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Service;


use Admin\Entity\WeChat;
use Admin\Entity\WeChatTag;
use Ramsey\Uuid\Uuid;


class WeChatTagManager extends BaseEntityManager
{


    /**
     * @param WeChat $weChat
     * @return int
     */
    public function getTagsCountByWeChat($weChat)
    {
        $qb = $this->resetQb();

        $qb->select($qb->expr()->count('t.id'));
        $qb->from(WeChatTag::class, 't');

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
    public function getTagsWithLimitPageByWeChat($weChat, $page = 1, $size = 10)
    {
        $qb = $this->resetQb();

        $qb->select('t')->from(WeChatTag::class, 't');

        $qb->where($qb->expr()->eq('t.weChat', '?1'));
        $qb->setParameter(1, $weChat);

        $qb->setMaxResults($size)->setFirstResult(($page -1) * $size);

        $qb->orderBy('t.tagid', 'ASC');

        return $this->getEntitiesFromPersistence();
    }


    /**
     *
     * @param $weChat
     * @return array
     */
    public function getAllTagsByWeChat($weChat)
    {
        return $this->getTagsWithLimitPageByWeChat($weChat, 1, 100);
    }


    /**
     * @param array $tags
     * @param WeChat $weChat
     * @return int
     */
    public function resetTags($tags, $weChat)
    {
        $qb = $this->resetQb();

        $qb->delete(WeChatTag::class, 't');
        $qb->where($qb->expr()->eq('t.weChat', '?1'));
        $qb->setParameter(1, $weChat);
        $qb->getQuery()->execute();

        $entities = [];
        foreach ($tags as $tag) {
            $entity = new WeChatTag();
            $entity->setId(Uuid::uuid1()->toString());
            $entity->setTagid($tag['id']);
            $entity->setTagname($tag['name']);
            $entity->setTagcount($tag['count']);
            $entity->setWeChat($weChat);
            $entities[] = $entity;
        }
        if (count($entities)) {
            $this->saveModifiedEntities($entities);
        }

        return count($entities);
    }

}