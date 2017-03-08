<?php
/**
 * WeChatMenuManager.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Service;


use Admin\Entity\WeChat;
use Admin\Entity\WeChatMenu;
use Ramsey\Uuid\Uuid;


class WeChatMenuManager extends BaseEntityManager
{


    /**
     * @param string $menuId
     * @return WeChatMenu
     */
    public function getWeChatMenu($menuId)
    {
        $qb = $this->resetQb();

        $qb->select('t')->from(WeChatMenu::class, 't');
        $qb->where($qb->expr()->eq('t.id', '?1'));
        $qb->setParameter(1, $menuId);

        return $this->getEntityFromPersistence();
    }


    /**
     * @param WeChat $weChat
     * @return int
     */
    public function getMenuCountByWeChat($weChat)
    {
        $qb = $this->resetQb();

        $qb->select($qb->expr()->count('t.id'));
        $qb->from(WeChatMenu::class, 't');

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
    public function getMenusWithLimitPageByWeChat($weChat, $page = 1, $size = 10)
    {
        $qb = $this->resetQb();

        $qb->select('t')->from(WeChatMenu::class, 't');

        $qb->where($qb->expr()->eq('t.weChat', '?1'));
        $qb->setParameter(1, $weChat);

        $qb->setMaxResults($size)->setFirstResult(($page -1) * $size);

        $qb->orderBy('t.type', 'ASC')->addOrderBy('t.updated', 'DESC');

        return $this->getEntitiesFromPersistence();
    }


    /**
     * @param WeChat $weChat
     * @param string $name
     * @param string $menu
     */
    public function createDefaultWeChatMenu($weChat, $name, $menu)
    {
        $menuEntity = new WeChatMenu();
        $menuEntity->setId(Uuid::uuid1()->toString());
        $menuEntity->setName($name);
        $menuEntity->setMenu($menu);
        $menuEntity->setType(WeChatMenu::TYPE_DEFAULT);
        $menuEntity->setCond('');
        $menuEntity->setUpdated(new \DateTime());
        $menuEntity->setWeChat($weChat);

        $this->saveModifiedEntity($menuEntity);
    }


    /**
     * @param WeChat $weChat
     * @param string $name
     * @param string $menu
     * @param string $cond
     */
    public function createConditionalWeChatMenu($weChat, $name, $menu, $cond)
    {
        $menuEntity = new WeChatMenu();
        $menuEntity->setId(Uuid::uuid1()->toString());
        $menuEntity->setName($name);
        $menuEntity->setMenu($menu);
        $menuEntity->setType(WeChatMenu::TYPE_CONDITIONAL);
        $menuEntity->setCond($cond);
        $menuEntity->setUpdated(new \DateTime());
        $menuEntity->setWeChat($weChat);

        $this->saveModifiedEntity($menuEntity);
    }


    /**
     * @param WeChat $weChat
     * @param string $name
     * @param string $menu
     * @param int $type
     */
    public function createWeChatMenu($weChat, $name, $menu, $type)
    {
        $menuEntity = new WeChatMenu();
        $menuEntity->setId(Uuid::uuid1()->toString());
        $menuEntity->setName($name);
        $menuEntity->setMenu($menu);
        $menuEntity->setType($type);
        $menuEntity->setUpdated(new \DateTime());
        $menuEntity->setWeChat($weChat);

        $this->saveModifiedEntity($menuEntity);
    }



}