<?php
/**
 * WeChatMenuController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Controller;


use Admin\Entity\WeChat;
use Admin\Entity\WeChatMenu;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class WeChatMenuController extends AdminBaseController
{

    /**
     * 菜单列表
     */
    public function indexAction()
    {
        $myself = $this->getMemberManager()->getCurrentMember();

        $weChat = $this->getWeChatManager()->getWeChatByMember($myself);

        if (!$weChat instanceof WeChat) {
            return $this->go(
                '没有配置公众号',
                '未查询到您的公众号信息, 无法继续操作. 您需要先配置您的公众号信息!',
                $this->url()->fromRoute('admin/weChat')
            );
        }

        // Page configuration
        $size = 10;
        $page = (int)$this->params()->fromRoute('key', 1);
        if ($page < 1) { $page = 1; }

        $wm = $this->getWeChatMenuManager();

        $count = $wm->getMenuCountByWeChat($weChat);

        // Get pagination helper
        $viewHelperManager = $this->getSm('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/weChatMenu', ['action' => 'index', 'key' => '%d']));

        // List data
        $rows = $wm->getMenusWithLimitPageByWeChat($weChat, $page, $size);

        return new ViewModel([
            'weChat' => $weChat,
            'menus' => $rows,
            'activeId' => __METHOD__,
        ]);

    }


    /**
     * 添加菜单
     */
    public function addAction()
    {

        $myself = $this->getMemberManager()->getCurrentMember();

        $weChat = $this->getWeChatManager()->getWeChatByMember($myself);

        if (!$weChat instanceof WeChat) {
            return $this->go(
                '没有配置公众号',
                '未查询到您的公众号信息, 无法继续操作. 您需要先配置您的公众号信息!',
                $this->url()->fromRoute('admin/weChat')
            );
        }

        if($this->getRequest()->isPost()) {


            $menuTitle = strip_tags($this->params()->fromPost('menuTitle'));
            if (empty($menuTitle)) {
                return $this->go(
                    '需要设置菜单名称',
                    '您可以建立多套菜单, 需要给每套菜单设定一个名字方便管理!',
                    $this->url()->fromRoute('admin/weChatMenu', ['action' => 'add'])
                );
            }

            $menuBar = new \stdClass();
            $menuBar->button = [];

            $menuName = $this->params()->fromPost('menuName', []);
            foreach ($menuName as $k => $name) {

                $menuType = $this->params()->fromPost('menuType', []);

                if (!isset($menuType[$k])) {
                    continue;
                }

                $type = $menuType[$k];

                if ('parent' != $type) {

                    $menuValue = $this->params()->fromPost('menuValue', []);
                    $value = @$menuValue[$k]; //Filter...

                    if (!empty($value)) {
                        $menu = new \stdClass();
                        $menu->name = $name; // $name max length: 16 Bytes = 16/3 UTF-8 chars
                        $menu->type = $type;
                        if(in_array($type, ['media_id', 'view_limited'])) {
                            $menu->media_id = $value;
                        } else if('view' == $type) {
                            $menu->url = $value; // $value max length: 1024 Byte = 1024/3 UTF-8 chars
                        } else {
                            $menu->key = $value; // $value max length: 128 Byte = 128/3 UTF-8 chars
                        }
                        //array_push($menuBar->button, $menu);
                        $menuBar->button[] = $menu;
                    }
                } else { // sub menus
                    $subMenuName = $this->params()->fromPost('subMenuName', []);
                    $subMenuType = $this->params()->fromPost('subMenuType', []);
                    $subMenuValue = $this->params()->fromPost('subMenuValue', []);
                    if (isset($subMenuName[$k]) && isset($subMenuType[$k]) && isset($subMenuValue[$k])) {

                        $menu = new \stdClass();
                        $menu->name = $name;
                        $menu->sub_button = [];

                        $subNames = $subMenuName[$k];
                        $subTypes = $subMenuType[$k];
                        $subValues = $subMenuValue[$k];

                        foreach ($subNames as $subKey => $subName) {
                            $subType = @$subTypes[$subKey];
                            $subValue = @$subValues[$subKey];

                            if (!empty($subType) && !empty($subValue)) {
                                $subMenu = new \stdClass();
                                $subMenu->name = $subName; // $subName max length: 40Bytes
                                $subMenu->type = $subType;
                                if(in_array($subType, ['media_id', 'view_limited'])) {
                                    $subMenu->media_id = $subValue;
                                } else if('view' == $subType) {
                                    $subMenu->url = $subValue;
                                } else {
                                    $subMenu->key = $subValue;
                                }
                                //array_push($menu->sub_button, $subMenu);
                                $menu->sub_button[] = $subMenu;
                            }
                        }

                        //array_pull($menuBar->button, $menu);
                        $menuBar->button[] = $menu;
                    }
                }
            }

            if (count($menuBar->button) < 1) {
                return $this->go(
                    '菜单为空',
                    '请创建一个至少包含一个菜单的菜单配置单!',
                    $this->url()->fromRoute('admin/weChatMenu', ['action' => 'add'])
                );
            }


            $menuCategory = $this->params()->fromPost('menuCategory');
            $menuForSex = '';
            $menuForPlatform = '';
            $menuForTag = '';
            $menuForCountry = '';
            $menuForProvince = '';
            $menuForCity = '';
            $menuForLang = '';

            if (WeChatMenu::TYPE_CONDITIONAL == $menuCategory) {
                $menuForSex = $this->params()->fromPost('menuForSex');
                $menuForPlatform = $this->params()->fromPost('menuForPlatform');

                if (!empty($menuForSex) || !empty($menuForPlatform)) {
                    $condObj = new \stdClass();
                    $condObj->tag_id = $menuForTag;
                    $condObj->client_platform_type = $menuForPlatform;
                    $condObj->sex = $menuForSex;
                    $condObj->country = $menuForCountry;
                    $condObj->province = $menuForProvince;
                    $condObj->city = $menuForCity;
                    $condObj->language = $menuForLang;

                    $menuBar->matchrule = $condObj;
                } else {
                    $menuCategory = WeChatMenu::TYPE_DEFAULT;
                }
            } else {
                $menuCategory = WeChatMenu::TYPE_DEFAULT;
            }

            $this->getWeChatMenuManager()->createWeChatMenu($weChat, $menuTitle, json_encode($menuBar, JSON_UNESCAPED_UNICODE), $menuCategory);

            return $this->go(
                '菜单已创建',
                '微信菜单: ' . $menuTitle . ' 已经创建!',
                $this->url()->fromRoute('admin/weChatMenu')
            );
        }

        return new ViewModel([
            'weChat' => $weChat,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 编辑菜单
     */
    public function editAction()
    {
        $menuId = (string)$this->params()->fromRoute('key');
        $wm = $this->getWeChatMenuManager();

        $weChatMenu = $wm->getWeChatMenu($menuId);
        if (!$weChatMenu instanceof WeChatMenu) {
            return $this->go(
                '菜单无效',
                '未查询到您的菜单信息信息, 请确认你的菜单信息信息没有错误!',
                $this->url()->fromRoute('admin/weChat')
            );
        }

        $weChat = $weChatMenu->getWeChat();
        if (!$weChat instanceof WeChat) {
            return $this->go(
                '没有配置公众号',
                '未查询到您的公众号信息, 无法继续操作. 您需要先配置您的公众号信息!',
                $this->url()->fromRoute('admin/weChat')
            );
        }

        $myself = $this->getMemberManager()->getCurrentMember();
        if ($myself->getMemberId() != $weChat->getMember()->getMemberId()) {
            throw new \RunException('禁止操作不属于您的公众号的菜单信息!');
        }


        if($this->getRequest()->isPost()) {

            $menuTitle = strip_tags($this->params()->fromPost('menuTitle'));
            if (empty($menuTitle)) {
                return $this->go(
                    '需要设置菜单名称',
                    '您可以建立多套菜单, 需要给每套菜单设定一个名字方便管理!',
                    $this->url()->fromRoute('admin/weChatMenu', ['action' => 'edit', 'key' => $menuId])
                );
            }

            $menuBar = new \stdClass();
            $menuBar->button = [];

            $menuName = $this->params()->fromPost('menuName', []);
            foreach ($menuName as $k => $name) {

                $menuType = $this->params()->fromPost('menuType', []);

                if (!isset($menuType[$k])) {
                    continue;
                }

                $type = $menuType[$k];

                if ('parent' != $type) {

                    $menuValue = $this->params()->fromPost('menuValue', []);
                    $value = @$menuValue[$k]; //Filter...

                    if (!empty($value)) {
                        $menu = new \stdClass();
                        $menu->name = $name; // $name max length: 16 Bytes = 16/3 UTF-8 chars
                        $menu->type = $type;
                        if(in_array($type, ['media_id', 'view_limited'])) {
                            $menu->media_id = $value;
                        } else if('view' == $type) {
                            $menu->url = $value; // $value max length: 1024 Byte = 1024/3 UTF-8 chars
                        } else {
                            $menu->key = $value; // $value max length: 128 Byte = 128/3 UTF-8 chars
                        }
                        //array_push($menuBar->button, $menu);
                        $menuBar->button[] = $menu;
                    }
                } else { // sub menus
                    $subMenuName = $this->params()->fromPost('subMenuName', []);
                    $subMenuType = $this->params()->fromPost('subMenuType', []);
                    $subMenuValue = $this->params()->fromPost('subMenuValue', []);
                    if (isset($subMenuName[$k]) && isset($subMenuType[$k]) && isset($subMenuValue[$k])) {

                        $menu = new \stdClass();
                        $menu->name = $name;
                        $menu->sub_button = [];

                        $subNames = $subMenuName[$k];
                        $subTypes = $subMenuType[$k];
                        $subValues = $subMenuValue[$k];

                        foreach ($subNames as $subKey => $subName) {
                            $subType = @$subTypes[$subKey];
                            $subValue = @$subValues[$subKey];

                            if (!empty($subType) && !empty($subValue) && !empty($subName)) {
                                $subMenu = new \stdClass();
                                $subMenu->name = $subName; // $subName max length: 40Bytes
                                $subMenu->type = $subType;
                                if(in_array($subType, ['media_id', 'view_limited'])) {
                                    $subMenu->media_id = $subValue;
                                } else if('view' == $subType) {
                                    $subMenu->url = $subValue;
                                } else {
                                    $subMenu->key = $subValue;
                                }
                                //array_push($menu->sub_button, $subMenu);
                                $menu->sub_button[] = $subMenu;
                            }
                        }

                        //array_pull($menuBar->button, $menu);
                        $menuBar->button[] = $menu;
                    }
                }
            }

            if (count($menuBar->button) < 1) {
                return $this->go(
                    '菜单为空',
                    '请至少保留一个菜单的菜单配置单!',
                    $this->url()->fromRoute('admin/weChatMenu', ['action' => 'edit', 'key' => $menuId])
                );
            }


            $menuCategory = $this->params()->fromPost('menuCategory');
            $menuForSex = '';
            $menuForPlatform = '';
            $menuForTag = '';
            $menuForCountry = '';
            $menuForProvince = '';
            $menuForCity = '';
            $menuForLang = '';

            if (WeChatMenu::TYPE_CONDITIONAL == $menuCategory) {
                $menuForSex = $this->params()->fromPost('menuForSex');
                $menuForPlatform = $this->params()->fromPost('menuForPlatform');

                if (!empty($menuForSex) || !empty($menuForPlatform)) {
                    $condObj = new \stdClass();
                    $condObj->tag_id = $menuForTag;
                    $condObj->client_platform_type = $menuForPlatform;
                    $condObj->sex = $menuForSex;
                    $condObj->country = $menuForCountry;
                    $condObj->province = $menuForProvince;
                    $condObj->city = $menuForCity;
                    $condObj->language = $menuForLang;

                    $menuBar->matchrule = $condObj;
                } else {
                    $menuCategory = WeChatMenu::TYPE_DEFAULT;
                }
            } else {
                $menuCategory = WeChatMenu::TYPE_DEFAULT;
            }

            $weChatMenu->setName($menuTitle);
            $weChatMenu->setMenu(json_encode($menuBar, JSON_UNESCAPED_UNICODE));
            $weChatMenu->setType($menuCategory);
            $weChatMenu->setUpdated(new \DateTime());

            $wm->saveModifiedEntity($weChatMenu);

            return $this->go(
                '菜单已更新',
                '微信菜单: ' . $menuTitle . ' 已经更新到本地!',
                $this->url()->fromRoute('admin/weChatMenu')
            );
        }

        return new ViewModel([
            'menu' => $weChatMenu,
            'activeId' => __CLASS__,
        ]);

    }


    /**
     * 删除菜单
     */
    public function deleteAction()
    {
        $menuId = (string)$this->params()->fromRoute('key');
        $wm = $this->getWeChatMenuManager();

        $weChatMenu = $wm->getWeChatMenu($menuId);
        if (!$weChatMenu instanceof WeChatMenu) {
            return $this->go(
                '菜单无效',
                '未查询到您的菜单信息信息, 请确认你的菜单信息信息没有错误!',
                $this->url()->fromRoute('admin/weChat')
            );
        }

        $weChat = $weChatMenu->getWeChat();
        if (!$weChat instanceof WeChat) {
            return $this->go(
                '没有配置公众号',
                '未查询到您的公众号信息, 无法继续操作. 您需要先配置您的公众号信息!',
                $this->url()->fromRoute('admin/weChat')
            );
        }

        $myself = $this->getMemberManager()->getCurrentMember();
        if ($myself->getMemberId() != $weChat->getMember()->getMemberId()) {
            throw new \RunException('禁止操作不属于您的公众号的菜单信息!');
        }

        if (WeChatMenu::STATUS_ACTIVATED == $weChatMenu->getStatus()) {
            return $this->go(
                '禁止删除',
                '该菜单已在使用中, 请先先清空微信公众号菜单, 再删除本地菜单.',
                $this->url()->fromRoute('admin/weChatMenu')
            );
        }

        $wm->removeEntity($weChatMenu);

        return $this->go(
            '菜单已删除',
            '您的本地菜单已经完全被删除. 已同步到公众号的不受影响.',
            $this->url()->fromRoute('admin/weChatMenu')
        );
    }


    /**
     * 同步微信菜单
     */
    public function asyncAction()
    {
        $result = ['success' => false, 'code' => 0, 'message' => ''];

        $menuId = (string)$this->params()->fromRoute('key');
        $wm = $this->getWeChatMenuManager();

        $weChatMenu = $wm->getWeChatMenu($menuId);
        if (!$weChatMenu instanceof WeChatMenu) {
            $result['message'] = '未查询到您的菜单信息信息, 请确认你的菜单信息信息没有错误!';
            return new JsonModel($result);
        }

        $weChat = $weChatMenu->getWeChat();
        if (!$weChat instanceof WeChat) {
            $result['message'] = '未查询到您的公众号信息, 无法继续操作. 您需要先配置您的公众号信息!';
            return new JsonModel($result);
        }

        $myself = $this->getMemberManager()->getCurrentMember();
        if ($myself->getMemberId() != $weChat->getMember()->getMemberId()) {
            $result['message'] = '禁止操作不属于您的公众号的菜单信息!';
            return new JsonModel($result);
        }

        $ws = $this->getWeChatService($weChat->getWxId());

        if(WeChatMenu::TYPE_DEFAULT == $weChatMenu->getType()) {
            $ws->removeAllMenus(); //Delete all remote menu
            $ws->createDefaultMenu($weChatMenu->getMenu()); //Create default menu

            //Reset all local menu status
            $menus = $weChat->getMenus();
            $updated = [];
            foreach($menus as $menu) {
                if($menu instanceof WeChatMenu) {
                    if($menu->getId() != $weChatMenu->getId()) {
                        $menu->setStatus(WeChatMenu::STATUS_RETIRED);
                        $updated[] = $menu;
                    }
                }
            }
            $weChatMenu->setStatus(WeChatMenu::STATUS_ACTIVATED);
            $updated[] = $weChatMenu;

            $wm->saveModifiedEntities($updated);
        } else {
            $count = $wm->getActivatedMenuCountByWeChatWithType($weChat, WeChatMenu::TYPE_CONDITIONAL);
            if($count > 2) {
                $result['message'] = '个性化菜单已经使用满额!';
                return new JsonModel($result);
            }

            //todo
        }
    }






    /**
     *  ACL 登记
     *
     * @return array
     */
    public static function ComponentRegistry()
    {
        $item = self::CreateControllerRegistry(__CLASS__, '公众号菜单', 'admin/weChatMenu', 1, 'list', 21);

        $item['actions']['index'] = self::CreateActionRegistry('index', '菜单列表', 1, 'bars', 9);
        $item['actions']['add'] = self::CreateActionRegistry('add', '增加菜单', 1, 'plus', 6);
        $item['actions']['edit'] = self::CreateActionRegistry('edit', '编辑菜单');
        $item['actions']['delete'] = self::CreateActionRegistry('delete', '删除菜单');
        $item['actions']['async'] = self::CreateActionRegistry('async', '同步菜单');

        return $item;
    }


}