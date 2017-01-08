<?php
/**
 * BaseController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Controller;


use Zend\Mvc\Controller\AbstractActionController;

abstract class BaseController extends AbstractActionController
{

    /**
     * Get the controller actions information
     *
     * @return array
     */
    abstract public function autoRegisterComponent();


}