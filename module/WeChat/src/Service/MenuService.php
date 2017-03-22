<?php
/**
 * MenuService.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace WeChat\Service;


use Ramsey\Uuid\Uuid;
use WeChat\Entity\Account;
use WeChat\Entity\Menu;
use WeChat\Exception\InvalidArgumentException;


class MenuService extends BaseEntityService
{


    /**
     * @param string $menuId
     * @return Menu
     * @throws InvalidArgumentException
     */
    public function getWeChatMenu($menuId)
    {
        $qb = $this->resetQb();

        $qb->select('t')->from(Menu::class, 't');
        $qb->where($qb->expr()->eq('t.id', '?1'));
        $qb->setParameter(1, $menuId);

        $menu = $this->getEntityFromPersistence();
        if(!$menu instanceof Menu) {
            throw new InvalidArgumentException('无效的菜单编号:' . $menuId);
        }
        return $menu;
    }


    /**
     * @param Account $weChat
     * @param int $type
     * @return int
     */
    public function getActivatedMenuCountByWeChatWithType($weChat, $type)
    {
        $qb = $this->resetQb();

        $qb->select($qb->expr()->count('t.id'));
        $qb->from(Menu::class, 't');

        $qb->where($qb->expr()->andX(
            $qb->expr()->eq('t.weChat', '?1'),
            $qb->expr()->eq('t.type', '?2'),
            $qb->expr()->eq('t.status', '?3')
        ));
        $qb->setParameter(1, $weChat)->setParameter(2, $type)->setParameter(3, Menu::STATUS_ACTIVATED);

        return $this->getEntitiesCount();
    }


    /**
     * @param Account $weChat
     * @return int
     */
    public function getMenuCountByWeChat($weChat)
    {
        $qb = $this->resetQb();

        $qb->select($qb->expr()->count('t.id'));
        $qb->from(Menu::class, 't');

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
    public function getMenusWithLimitPageByWeChat($weChat, $page = 1, $size = 10)
    {
        $qb = $this->resetQb();

        $qb->select('t')->from(Menu::class, 't');

        $qb->where($qb->expr()->eq('t.weChat', '?1'));
        $qb->setParameter(1, $weChat);

        $qb->setMaxResults($size)->setFirstResult(($page -1) * $size);

        $qb->orderBy('t.status', 'DESC')->addOrderBy('t.type', 'ASC')->addOrderBy('t.updated', 'DESC');

        return $this->getEntitiesFromPersistence();
    }


    /**
     * @param Account $weChat
     */
    public function deleteWeChatMenu($weChat)
    {
        $qb = $this->resetQb();

        $qb->delete(Menu::class, 't');
        $qb->where($qb->expr()->eq('t.weChat', '?1'));
        $qb->setParameter(1, $weChat);
        $qb->getQuery()->execute();
    }

    /**
     * @param Account $weChat
     */
    public function resetWeChatMenu($weChat)
    {
        $qb = $this->resetQb();

        $qb->update(Menu::class, 't');
        $qb->set('t.status', '?1');
        $qb->where($qb->expr()->eq('t.weChat', '?2'));
        $qb->setParameter(1, Menu::STATUS_RETIRED);
        $qb->setParameter(2, $weChat);
        $qb->getQuery()->execute();
    }


    /**
     * @param Account $weChat
     * @param string $name
     * @param string $menu
     * @param int $type
     * @param string $menuid
     * @param int $status
     */
    public function createWeChatMenu($weChat, $name, $menu, $type, $menuid = '', $status = Menu::STATUS_RETIRED)
    {
        $menuEntity = new Menu();
        $menuEntity->setId(Uuid::uuid1()->toString());
        $menuEntity->setName($name);
        $menuEntity->setMenuid($menuid);
        $menuEntity->setMenu($menu);
        $menuEntity->setType($type);
        $menuEntity->setStatus($status);
        $menuEntity->setUpdated(new \DateTime());
        $menuEntity->setWeChat($weChat);

        $this->saveModifiedEntity($menuEntity);
    }



}