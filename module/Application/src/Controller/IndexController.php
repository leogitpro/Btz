<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Service\MailManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;



class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        return new ViewModel();
    }

    public function testAction()
    {
        //return $this->getResponse();
        return new ViewModel();
    }


    /**
     * Common service for send mail
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function sendMailAction()
    {
        ignore_user_abort(true);
        set_time_limit(0);

        $subject = $this->params()->fromPost('mail_subject');
        $content = $this->params()->fromPost('mail_content');
        $recipient = $this->params()->fromPost('mail_recipient');

        if (empty($subject) || empty($content) || empty($recipient)) {
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Mail params lost. send cancel.');
            return $this->getResponse();
        }

        $this->getLoggerPlugin()->debug("Start send mail");

        $serviceManager = $this->getEvent()->getApplication()->getServiceManager();
        $mailService = $serviceManager->get(MailManager::class);
        $mailService->sendMail($recipient, $subject, $content);

        $this->getLoggerPlugin()->debug("End send mail");

        return $this->getResponse();
    }
}
