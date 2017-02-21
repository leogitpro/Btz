<?php
/**
 * BaseController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Controller;


use Zend\Mvc\Controller\AbstractActionController;




class BaseController extends AbstractActionController
{

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