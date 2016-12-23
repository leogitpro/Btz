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
        //var_dump($this->host()->getHost());

        //$arr = $this->config()->get('mail.smtp.connection_config');
        //echo '<pre>'; print_r($arr); echo '</pre>';

        return new ViewModel();
    }

    public function testAction()
    {


        $title = 'Test title';
        $content = 'Welcome to the share center. It\'s a free community for everyone everything. just tell what\'s your want. thanks.';
        $url_href = '#';
        $url_title = 'Google';
        $delay = 5;

        //return $this->display($title, $content);
        return $this->display($title, $content, $url_href, $url_title);
        //return $this->display($title, $content, $url_href, $url_title, $delay);




        return new ViewModel();
    }
}
