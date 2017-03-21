<?php
/**
 * TagService.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace WeChat\Service;


use Ramsey\Uuid\Uuid;
use WeChat\Entity\Account;
use WeChat\Entity\Tag;


class TagService extends BaseEntityService
{

    /**
     * @param Account $weChat
     * @return int
     */
    public function getTagsCountByWeChat($weChat)
    {
        $qb = $this->resetQb();

        $qb->select($qb->expr()->count('t.id'));
        $qb->from(Tag::class, 't');

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
    public function getTagsWithLimitPageByWeChat($weChat, $page = 1, $size = 10)
    {
        $qb = $this->resetQb();

        $qb->select('t')->from(Tag::class, 't');

        $qb->where($qb->expr()->eq('t.weChat', '?1'));
        $qb->setParameter(1, $weChat);

        $qb->setMaxResults($size)->setFirstResult(($page -1) * $size);

        $qb->orderBy('t.tagid', 'ASC');

        return $this->getEntitiesFromPersistence();
    }


    /**
     *
     * @param Account $weChat
     * @return array
     */
    public function getAllTagsByWeChat($weChat)
    {
        return $this->getTagsWithLimitPageByWeChat($weChat, 1, 100);
    }


    /**
     * @param Account $weChat
     */
    public function deleteAllTagsForWeChat($weChat)
    {
        $qb = $this->resetQb();

        $qb->delete(Tag::class, 't');
        $qb->where($qb->expr()->eq('t.weChat', '?1'));
        $qb->setParameter(1, $weChat);
        $qb->getQuery()->execute();
    }


    /**
     * @param array $tags
     * @param Account $weChat
     * @return int
     */
    public function resetTags($tags, $weChat)
    {
        $this->deleteAllTagsForWeChat($weChat);

        $entities = [];
        foreach ($tags as $tag) {
            $entity = new Tag();
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