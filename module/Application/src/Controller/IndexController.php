<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Form\ContactUsForm;
use Application\Service\ContactManager;
use Application\Service\MailManager;
use Application\Service\NavManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;



class IndexController extends AbstractActionController
{

    /**
     * @var ContactManager
     */
    private $contactManager;

    /**
     * @var MailManager
     */
    private $mailManager;

    /**
     * @var NavManager
     */
    private $navManager;


    /**
     * IndexController constructor.
     *
     * @param ContactManager $contactManager
     * @param MailManager $mailManager
     * @param NavManager $navManager
     */
    public function __construct(ContactManager $contactManager, MailManager $mailManager, NavManager $navManager)
    {
        $this->contactManager = $contactManager;
        $this->mailManager = $mailManager;
        $this->navManager = $navManager;
    }


    /**
     * Home page
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $ip = $this->getServerPlugin()->ipAddress();
        $ua = $this->getServerPlugin()->userAgent();

        echo '<p>ip: ' . $ip . '</p>';
        echo '<p>ua: ' . $ua . '</p>';

        echo '<pre>'; print_r($_SESSION); echo '</pre>';

        return new ViewModel();
    }

    /**
     * Test page
     *
     * @return ViewModel
     */
    public function testAction()
    {
        //return $this->getResponse();
        return new ViewModel();
    }


    /**
     * Contact page
     *
     * @return ViewModel
     */
    public function contactAction()
    {
        $captchaConfig = $this->getConfigPlugin()->get('captcha');
        $captchaConfig['imgUrl'] = $this->getRequest()->getBaseUrl() . $captchaConfig['imgUrl'];
        $form = new ContactUsForm($captchaConfig);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $email = $data['email'];
                $subject = $data['subject'];
                $message = $data['message'];
                $ip = $this->getServerPlugin()->ipAddress();

                $this->contactManager->saveNewContact($email, $subject, $message, $ip);

                // Ready send mail
                $contactMail = $this->getConfigPlugin()->get('mail.contact');

                $mailTpl = $this->getConfigPlugin()->get('mail.template.contact');
                $mailTpl = str_replace('%email%', $email, $mailTpl);
                $mailTpl = str_replace('%message%', $message, $mailTpl);
                $mailTpl = str_replace('%datetime%', date('Y-m-d H:i:s A D'), $mailTpl);

                $postData = [
                    'mail_subject' => $subject,
                    'mail_content' => $mailTpl,
                    'mail_recipient' => $contactMail,
                ];

                $asyncUrl = $this->url()->fromRoute('send-mail');
                $this->getLoggerPlugin()->debug("Start call async request:" . $asyncUrl);
                $this->getAsyncRequestPlugin()->post($this->getServerPlugin()->domain() . $asyncUrl, $postData);
                $this->getLoggerPlugin()->debug("Finished call async request");

                return $this->getDisplayPlugin()->show(
                    'Thank You!',
                    'We will respond to the E-mail address you have provided ASAP.'
                );
            }
        }

        return new ViewModel([
            'form' => $form,
        ]);
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
        $this->mailManager->sendMail($recipient, $subject, $content);
        $this->getLoggerPlugin()->debug("End send mail");

        return $this->getResponse();
    }
}
