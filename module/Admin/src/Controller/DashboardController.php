<?php
/**
 * Dashboard controller
 *
 * User: leo
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DashboardController extends AbstractActionController
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

}