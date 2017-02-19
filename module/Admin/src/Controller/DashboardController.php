<?php
/**
 * Dashboard controller
 *
 * User: leo
 */

namespace Admin\Controller;

use Admin\Entity\Member;
use Admin\Service\MemberManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class DashboardController extends AbstractActionController
{

    /**
     * @var MemberManager
     */
    private $memberManager;

    public function onDispatch(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();
        $this->memberManager = $sm->get(MemberManager::class);

        return parent::onDispatch($e);
    }


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