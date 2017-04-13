<?php
/**
 * WeChatMenuController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Controller;


use WeChat\Entity\Account;
use WeChat\Entity\Menu;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


/**
 * 公众号菜单管理
 *
 * Class WeChatMenuController
 * @package Admin\Controller
 */
class WeChatMenuController extends AdminBaseController
{

    /**
     * 菜单列表
     */
    public function indexAction()
    {
        $myself = $this->getMemberManager()->getCurrentMember();
        $weChat = $this->getWeChatAccountService()->getWeChatByMember($myself);

        // Page configuration
        $size = 10;
        $page = (int)$this->params()->fromRoute('key', 1);
        if ($page < 1) { $page = 1; }

        $count = $this->getWeChatMenuService()->getMenuCountByWeChat($weChat);

        // Get pagination helper
        $viewHelperManager = $this->getSm('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/weChatMenu', ['action' => 'index', 'key' => '%d']));

        // List data
        $rows = $this->getWeChatMenuService()->getMenusWithLimitPageByWeChat($weChat, $page, $size);

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
        $weChat = $this->getWeChatAccountService()->getWeChatByMember($myself);

        $tags = $this->getWeChatTagService()->getAllTagsByWeChat($weChat);

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


            if (Menu::TYPE_CONDITIONAL == $menuCategory) {
                $menuForSex = (string)$this->params()->fromPost('menuForSex', '');
                $menuForPlatform = (string)$this->params()->fromPost('menuForPlatform', '');
                $menuForTag = (string)$this->params()->fromPost('menuForTag', '');

                $menuForCountry = (string)$this->params()->fromPost('menuForCountry', '');
                $menuForProvince = (string)$this->params()->fromPost('menuForProvince', '');
                $menuForCity = (string)$this->params()->fromPost('menuForCity', '');
                $menuForLang = (string)$this->params()->fromPost('menuForLang', '');

                if (!empty($menuForSex) ||
                    !empty($menuForPlatform) ||
                    !empty($menuForTag) ||
                    !empty($menuForCountry) ||
                    !empty($menuForProvince) ||
                    !empty($menuForCity) ||
                    !empty($menuForLang)) {
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
                    $menuCategory = Menu::TYPE_DEFAULT;
                }
            } else {
                $menuCategory = Menu::TYPE_DEFAULT;
            }

            $this->getWeChatMenuService()->createWeChatMenu($weChat, $menuTitle, json_encode($menuBar, JSON_UNESCAPED_UNICODE), $menuCategory);

            return $this->go(
                '菜单已创建',
                '微信菜单: ' . $menuTitle . ' 已经创建!',
                $this->url()->fromRoute('admin/weChatMenu')
            );
        }

        return new ViewModel([
            'weChat' => $weChat,
            'tags' => $tags,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 编辑菜单
     */
    public function editAction()
    {
        $menuId = (string)$this->params()->fromRoute('key');
        $weChatMenu = $this->getWeChatMenuService()->getWeChatMenu($menuId);

        $weChat = $weChatMenu->getWeChat();
        if (!$weChat instanceof Account) {
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

        $tags = $this->getWeChatTagService()->getAllTagsByWeChat($weChat);


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

            if (Menu::TYPE_CONDITIONAL == $menuCategory) {
                $menuForSex = (string)$this->params()->fromPost('menuForSex', '');
                $menuForPlatform = (string)$this->params()->fromPost('menuForPlatform', '');
                $menuForTag = (string)$this->params()->fromPost('menuForTag', '');

                $menuForCountry = (string)$this->params()->fromPost('menuForCountry', '');
                $menuForProvince = (string)$this->params()->fromPost('menuForProvince', '');
                $menuForCity = (string)$this->params()->fromPost('menuForCity', '');
                $menuForLang = (string)$this->params()->fromPost('menuForLang', '');


                if (!empty($menuForSex) ||
                    !empty($menuForPlatform) ||
                    !empty($menuForTag) ||
                    !empty($menuForCountry) ||
                    !empty($menuForProvince) ||
                    !empty($menuForCity) ||
                    !empty($menuForLang)) {
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
                    $menuCategory = Menu::TYPE_DEFAULT;
                }
            } else {
                $menuCategory = Menu::TYPE_DEFAULT;
            }

            $weChatMenu->setName($menuTitle);
            $weChatMenu->setMenu(json_encode($menuBar, JSON_UNESCAPED_UNICODE));
            $weChatMenu->setType($menuCategory);
            $weChatMenu->setUpdated(new \DateTime());

            $this->getWeChatMenuService()->saveModifiedEntity($weChatMenu);

            return $this->go(
                '菜单已更新',
                '微信菜单: ' . $menuTitle . ' 已经更新到本地!',
                $this->url()->fromRoute('admin/weChatMenu')
            );
        }

        return new ViewModel([
            'menu' => $weChatMenu,
            'tags' => $tags,
            'activeId' => __CLASS__,
        ]);

    }


    /**
     * 删除菜单
     */
    public function deleteAction()
    {
        $menuId = (string)$this->params()->fromRoute('key');
        $weChatMenu = $this->getWeChatMenuService()->getWeChatMenu($menuId);

        $weChat = $weChatMenu->getWeChat();
        if (!$weChat instanceof Account) {
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

        if (Menu::STATUS_ACTIVATED == $weChatMenu->getStatus()) {
            return $this->go(
                '禁止删除',
                '该菜单已在使用中, 请先先清空微信公众号菜单, 再删除本地菜单.',
                $this->url()->fromRoute('admin/weChatMenu')
            );
        }

        $this->getWeChatMenuService()->removeEntity($weChatMenu);

        return $this->go(
            '菜单已删除',
            '您的本地菜单已经完全被删除. 已同步到公众号的不受影响.',
            $this->url()->fromRoute('admin/weChatMenu')
        );
    }


    /**
     * 同步菜单到微信平台
     */
    public function asyncAction()
    {
        $result = ['success' => false, 'code' => 0, 'message' => ''];

        $key = (string)$this->params()->fromRoute('key');
        $weChatMenu = $this->getWeChatMenuService()->getWeChatMenu($key);
        $weChat = $weChatMenu->getWeChat();
        if (!$weChat instanceof Account) {
            $result['message'] = '未查询到您的公众号信息, 无法继续操作. 您需要先配置您的公众号信息!';
            return new JsonModel($result);
        }

        $myself = $this->getMemberManager()->getCurrentMember();
        if ($myself->getMemberId() != $weChat->getMember()->getMemberId()) {
            $result['message'] = '禁止操作不属于您的公众号的菜单信息!';
            return new JsonModel($result);
        }

        if(Menu::TYPE_DEFAULT == $weChatMenu->getType()) {
            if (!$this->getWeChatService()->menuRemoveDefault($weChat)) { //Delete all remote menu
                $result['message'] = '公众号菜单清理失败!';
                return new JsonModel($result);
            }

            if (!$this->getWeChatService()->menuCreateDefault($weChat, $weChatMenu->getMenu())) { //Create default menu
                $result['message'] = '自定义菜单同步失败!';
                return new JsonModel($result);
            }

            //Reset all local menu status
            $menus = $weChat->getMenus();
            $updated = [];
            foreach($menus as $menu) {
                if($menu instanceof Menu) {
                    if($menu->getId() != $weChatMenu->getId()) {
                        $menu->setStatus(Menu::STATUS_RETIRED);
                        $menu->setMenuid('');
                        $updated[] = $menu;
                    }
                }
            }
            $weChatMenu->setStatus(Menu::STATUS_ACTIVATED);
            $updated[] = $weChatMenu;

            $this->getWeChatMenuService()->saveModifiedEntities($updated);
        } else {
            if(Menu::STATUS_ACTIVATED == $weChatMenu->getStatus()) { // Delete match menu
                if(!$this->getWeChatService()->menuRemoveConditional($weChat, $weChatMenu->getMenuid())) {
                    $result['message'] = '个性化菜单删除失败!';
                    return new JsonModel($result);
                } else {
                    $weChatMenu->setMenuid('');
                    $weChatMenu->setStatus(Menu::STATUS_RETIRED);

                    $this->getWeChatMenuService()->saveModifiedEntity($weChatMenu);
                }
            } else { //
                $count = $this->getWeChatMenuService()->getActivatedMenuCountByWeChatWithType($weChat, Menu::TYPE_CONDITIONAL);
                if($count > 2) {
                    $result['message'] = '个性化菜单已经使用满额! 不能再增加了.';
                    return new JsonModel($result);
                }

                $menuid = $this->getWeChatService()->menuCreateConditional($weChat, $weChatMenu->getMenu());
                if (empty($menuid)) {
                    $result['message'] = '个性化菜单添加失败!';
                    return new JsonModel($result);
                }

                $weChatMenu->setMenuid($menuid);
                $weChatMenu->setStatus(Menu::STATUS_ACTIVATED);
                $this->getWeChatMenuService()->saveModifiedEntity($weChatMenu);
            }
        }

        $result['success'] = true;
        $result['message'] = '菜单同步成功!';

        return new JsonModel($result);
    }


    /**
     * 清空微信平台菜单
     */
    public function trashAction()
    {
        $result = ['success' => false, 'code' => 0, 'message' => 'Invalid WeChat'];

        $myself = $this->getMemberManager()->getCurrentMember();
        $weChat = $this->getWeChatAccountService()->getWeChatByMember($myself);

        if(!$this->getWeChatService()->menuRemoveDefault($weChat)) {
            $result['message'] = '清空公众号菜单失败!';
            return new JsonModel($result);
        }

        $this->getWeChatMenuService()->resetWeChatMenu($weChat);

        $result['success'] = true;
        $result['message'] = '公众号菜单已经清理完毕.';

        return new JsonModel($result);
    }


    /**
     * 导入微信平台菜单到本地
     */
    public function importAction()
    {
        $result = ['success' => false, 'code' => 0, 'message' => 'Invalid WeChat'];

        $myself = $this->getMemberManager()->getCurrentMember();
        $weChat = $this->getWeChatAccountService()->getWeChatByMember($myself);

        $this->getWeChatMenuService()->deleteWeChatMenu($weChat);
        $menus = $this->getWeChatService()->menuExport($weChat);

        if(!empty($menus)) {
            $i = 0;
            foreach ($menus as $key => $menu) {
                $name = $i < 1 ? '自定义菜单' : '个性化菜单-' . $i;
                $type = $i < 1 ? Menu::TYPE_DEFAULT : Menu::TYPE_CONDITIONAL;
                $i++;
                $this->getWeChatMenuService()->createWeChatMenu($weChat, $name, json_encode($menu, JSON_UNESCAPED_UNICODE), $type, $key, Menu::STATUS_ACTIVATED);
            }
        }

        $result['success'] = true;
        $result['message'] = '公众号菜单已经导入完毕.';

        return new JsonModel($result);
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
        $item['actions']['delete'] = self::CreateActionRegistry('delete', '删除本地菜单');
        $item['actions']['async'] = self::CreateActionRegistry('async', '同步菜单到微信平台');
        $item['actions']['trash'] = self::CreateActionRegistry('trash', '清空微信平台菜单');
        $item['actions']['import'] = self::CreateActionRegistry('import', '导入微信平台菜单到本地');

        return $item;
    }


}