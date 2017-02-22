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


/**
 * Class AdminBaseController
 * @package Admin\Controller
 *
 * @method \Admin\Controller\Plugin\MessagePlugin getMessagePlugin()
 */
class AdminBaseController extends AppBaseController
{

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