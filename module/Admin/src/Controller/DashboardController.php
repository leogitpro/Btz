<?php
/**
 * Dashboard controller
 *
 * User: leo
 */

namespace Admin\Controller;


use Zend\View\Model\ViewModel;

class DashboardController extends BaseController
{

    public function autoRegisterComponent()
    {
        return [
            'controller' => __CLASS__,
            'name' => 'Dashboard',
            'route' => 'admin/dashboard',
            'menu' => true,
            'rank' => 999,
            'icon' => 'dashboard',
        ];
    }


    public function indexAction()
    {
        return new ViewModel();
    }

}