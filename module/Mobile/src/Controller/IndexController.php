<?php
/**
 * IndexController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Mobile\Controller;


use Zend\View\Model\ViewModel;


class IndexController extends MobileBaseController
{

    public function indexAction()
    {
        return new ViewModel();
    }
}