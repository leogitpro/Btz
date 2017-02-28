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
        //$config = $this->getConfigPlugin()->get('view_helpers');
        //echo '<pre>';print_r($config); echo '</pre>';

        return new ViewModel();
    }

}