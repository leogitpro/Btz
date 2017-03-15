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
     * Test page
     *
     * @return ViewModel
     */
    public function testAction()
    {
        return new ViewModel();
    }


    /**
     * 首页
     */
    public function indexAction()
    {
        return new ViewModel();
    }

    /**
     * 产品&服务
     */
    public function serviceAction()
    {
        return new ViewModel();
    }


    /**
     * 联络我们
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

                $this->getContactManager()->createContact($email, $subject, $message, $ip);

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
                    '谢谢您!',
                    '我们已经收到您的联络信息, 我们将在第一时间联络您. 请注意查收我们给你的回复邮件. 再次感谢!',
                    $this->url()->fromRoute('home'),
                    '返回',
                    5
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
