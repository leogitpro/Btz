<?php
/**
 * Created by PhpStorm.
 * User: leo
 * Date: 16/9/20
 * Time: PM3:40
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    public function indexAction()
    {
        if($this->access()->checkAccess('index')) {
            echo '<h1>Check passed</h1>';
        } else {
            echo '<h1>No authed</h1>';
        }
        return new ViewModel();
    }

    public function guideAction()
    {
        $vars = $this->params()->fromRoute();
        echo '<pre>'; print_r($vars); echo '</pre>';
        return new ViewModel();
    }

    public function detailAction() {

        $vars = $this->params()->fromRoute();
        echo '<pre>'; print_r($vars); echo '</pre>';
        return new ViewModel();
    }
}

