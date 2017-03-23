<?php
/**
 * Dashboard controller
 *
 * User: leo
 */

namespace Admin\Controller;


use Zend\View\Model\ViewModel;


class DashboardController extends AdminBaseController
{

    public function indexAction()
    {

        return new ViewModel();
    }

}