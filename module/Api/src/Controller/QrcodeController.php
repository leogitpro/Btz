<?php
/**
 * QrcodeController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Api\Controller;


use Zend\View\Model\JsonModel;

class QrcodeController extends ApiBaseController
{

    public function testAction()
    {
        $this->getResponse()->setContent('Hello, Baby!');
        //return new ViewModel();


        echo __METHOD__;

    }

    public function jsonAction()
    {
        return new JsonModel(['success' => true, 'code' => '1001']);
    }

}