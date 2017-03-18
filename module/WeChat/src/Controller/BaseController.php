<?php
/**
 * BaseController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace WeChat\Controller;


use Application\Controller\AppBaseController;
use Zend\Mvc\MvcEvent;


class BaseController extends AppBaseController
{

    public function onDispatch(MvcEvent $e)
    {
        parent::onDispatch($e);

        // Disable layout and view
        return $this->getResponse();
    }

}