<?php
/**
 * IndexController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Application\Controller;

use Application\Form\ContactUsForm;
use Zend\View\Model\ViewModel;


class IndexController extends AppBaseController
{

    /**
     * Home page
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        return new ViewModel();
    }

    /**
     * Test page
     *
     * @return ViewModel
     */
    public function testAction()
    {
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

                $this->getContactManager()->saveNewContact($email, $subject, $message, $ip);

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
     * 发送邮件
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
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '邮件发送参数非法! 取消发送.');
            return $this->getResponse();
        }

        $this->getLoggerPlugin()->debug("开始发送邮件: " . $subject);
        $this->getMailManager()->sendMail($recipient, $subject, $content);
        $this->getLoggerPlugin()->debug("邮件发送完毕: " . $subject);

        return $this->getResponse();
    }
}
