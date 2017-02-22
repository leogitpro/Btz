<?php
/**
 * Dashboard controller
 *
 * User: leo
 */

namespace Admin\Controller;


use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class DashboardController extends AdminBaseController
{


    public function indexAction()
    {
        return new ViewModel();
    }


    /**
     * Display forbidden page
     *
     * @return ViewModel
     */
    public function forbiddenAction()
    {
        return new ViewModel();
    }

    /**
     * Display forbid ajax call
     *
     * @return JsonModel
     */
    public function forbiddenajaxAction()
    {
        return new JsonModel(['success' => false, 'code' => 1001, 'message' => '您当前无权使用这个功能!']);
    }

}