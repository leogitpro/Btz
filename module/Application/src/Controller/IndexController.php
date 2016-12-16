<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        //$sm = $this->getEvent()->getApplication()->getServiceManager();
        //$logger = $sm->get("AppLogger");
        //$logger->emerg("emerg message test");

        //$config = $sm->get("config");
        //echo '<pre>'; print_r($config); echo '</pre>';

        return new ViewModel();
    }

    public function testAction()
    {
        return new ViewModel();
    }
}
