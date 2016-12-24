<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;


use Zend\Captcha\Factory;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;



class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        /**
        echo '<pre>';
        print_r($this->config()->get('log'));
        print_r($this->config()->get('service_manager'));
        echo '</pre>';
        //*/
        $sm = $this->getEvent()->getApplication()->getServiceManager();
        $logger = $sm->get("Logger");
        $logger->debug('This is a test debug at: ' . date('c'));
        $logger->info('iTest info log');
        $logger->emerg('Emerg message test');

        //$logger->emerg("emerg message test");

        //$config = $sm->get("config");
        //echo '<pre>'; print_r($config); echo '</pre>';
        //var_dump($this->host()->getHost());

        //$arr = $this->config()->get('mail.smtp.connection_config');
        //echo '<pre>'; print_r($arr); echo '</pre>';

        return new ViewModel();
    }

    public function testAction()
    {
        $captcha = Factory::factory($this->config()->get('captcha'));
        $id = $captcha->generate();

        var_dump($captcha->getWord());

        $imgUrl = $this->getRequest()->getBaseUrl() . $captcha->getImgUrl() . $captcha->getId() . $captcha->getSuffix();

        return new ViewModel(['img' => $imgUrl]);
    }
}
