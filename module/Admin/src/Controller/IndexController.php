<?php
/**
 * Default controller
 */

namespace Admin\Controller;


use Admin\Form\LoginForm;
use Admin\Service\AuthService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $sm = $this->getEvent()->getApplication()->getServiceManager();
        $authService = $sm->get(AuthService::class);

        if ($authService->hasIdentity()) {
            //todo
        } else {
            //$this->redirect()->toRoute('admin/default', ['action' => 'login', 'suffix' => '.html']);
        }

        return new ViewModel();
    }


    public function loginAction()
    {
        /**
        $form = new LoginForm();

        if($this->getRequest()->isPost())
        {
            $data = $this->params()->fromPost();
            $form->setData($data);

            if($form->isValid()) {
                //todo
            }
        }
        //*/

        return new ViewModel();
    }
}