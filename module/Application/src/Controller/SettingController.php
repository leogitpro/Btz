<?php
/**
 * Created by PhpStorm.
 * User: leo
 * Date: 16/9/20
 * Time: PM3:45
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SettingController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }
}
