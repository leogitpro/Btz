<?php
/**
 * BaseController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Controller;


use Admin\Service\AclManager;
use Admin\Service\AuthManager;
use Admin\Service\ComponentManager;
use Admin\Service\DepartmentManager;
use Admin\Service\FeedbackManager;
use Admin\Service\MemberManager;
use Admin\Service\MessageManager;
use Application\Controller\AppBaseController;
use WeChat\Service\AccountService;
use WeChat\Service\ClientService;
use WeChat\Service\InvoiceService;
use WeChat\Service\MenuService;
use WeChat\Service\OrderService;
use WeChat\Service\QrCodeService;
use WeChat\Service\TagService;
use WeChat\Service\WeChatService;


/**
 * Class AdminBaseController
 * @package Admin\Controller
 *
 * @method \Admin\Controller\Plugin\MessagePlugin getMessagePlugin()
 */
class AdminBaseController extends AppBaseController
{

    /**
     * @return WeChatService
     */
    protected function getWeChatService()
    {
        return $this->getSm(WeChatService::class);
    }

    /**
     * @return AccountService
     */
    protected function getWeChatAccountService()
    {
        return $this->getSm(AccountService::class);
    }

    /**
     * @return TagService
     */
    protected function getWeChatTagService()
    {
        return $this->getSm(TagService::class);
    }

    /**
     * @return ClientService
     */
    protected function getWeChatClientService()
    {
        return $this->getSm(ClientService::class);
    }

    /**
     * @return QrCodeService
     */
    protected function getWeChatQrCodeService()
    {
        return $this->getSm(QrCodeService::class);
    }

    /**
     * @return MenuService
     */
    protected function getWeChatMenuService()
    {
        return $this->getSm(MenuService::class);
    }


    /**
     * @return OrderService
     */
    protected function getWeChatOrderService()
    {
        return $this->getSm(OrderService::class);
    }

    /**
     * @return InvoiceService
     */
    protected function getWeChatInvoiceService()
    {
        return $this->getSm(InvoiceService::class);
    }

    ///


    /**
     * @return FeedbackManager
     */
    protected function getFeedbackManager()
    {
        return $this->getSm(FeedbackManager::class);
    }

    /**
     * @return MessageManager
     */
    protected function getMessageManager()
    {
        return $this->getSm(MessageManager::class);
    }

    /**
     * @return MemberManager
     */
    protected function getMemberManager()
    {
        return $this->getSm(MemberManager::class);
    }

    /**
     * @return DepartmentManager
     */
    protected function getDeptManager()
    {
        return $this->getSm(DepartmentManager::class);
    }

    /**
     * @return ComponentManager
     */
    protected function getComponentManager()
    {
        return $this->getSm(ComponentManager::class);
    }

    /**
     * @return AuthManager
     */
    protected function getAuthManager()
    {
        return $this->getSm(AuthManager::class);
    }

    /**
     * @return AclManager
     */
    protected function getAclManager()
    {
        return $this->getSm(AclManager::class);
    }


    /**
     * Show redirect page
     *
     * @param string $title
     * @param string $message
     * @param string $url
     * @param string $back
     * @param int $delay
     * @return mixed
     */
    protected function go($title, $message, $url, $back = '返回', $delay = 3)
    {
        return $this->getMessagePlugin()->show($title, $message, $url, $back, $delay);
    }


    /**
     * Get the controller actions information
     *
     * @return array
     */
    //public static function ComponentRegistry() {};



    /**
     * @param string $controller
     * @param string $name
     * @param string $route
     * @param int $menu
     * @param string $icon
     * @param int $rank
     * @return array
     */
    protected static function CreateControllerRegistry($controller, $name, $route, $menu = 0, $icon = 'list', $rank = 0)
    {
        if (empty($icon)) { $icon = 'list'; }
        return [
            'controller' => $controller,
            'name' => $name,
            'route' => $route,
            'menu' => $menu,
            'icon' => $icon,
            'rank' => $rank,
            'actions' => [],
        ];
    }


    /**
     * @param string $action
     * @param string $name
     * @param int $menu
     * @param string $icon
     * @param int $rank
     * @return array
     */
    protected static function CreateActionRegistry($action, $name, $menu = 0, $icon = 'caret-right', $rank = 0)
    {
        if (empty($icon)) { $icon = 'caret-right'; }
        return [
            'action' => $action,
            'name' => $name,
            'menu' => $menu,
            'icon' => $icon,
            'rank' => $rank,
        ];
    }


}