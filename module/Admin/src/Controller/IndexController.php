<?php
/**
 * Default controller
 */

namespace Admin\Controller;


use Admin\Form\LoginForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }


    public function loginAction()
    {
        //**
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

        return new ViewModel(['form' => $form]);
    }
}