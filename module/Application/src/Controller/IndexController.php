<?php
/**
 * IndexController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Application\Controller;

use Admin\Service\MemberManager;
use Admin\WeChat\Remote;
use Application\Form\ApplyForm;
use Application\Form\ContactUsForm;
use Mail\Exception\InvalidArgumentException;
use Mail\Exception\RuntimeException;
use WeChat\Service\NetworkManager;
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
        throw new \Exception('test index');
        return new ViewModel();
    }

    /**
     * 产品&服务
     */
    public function serviceAction()
    {
        return new ViewModel();
    }


    public function applyAction()
    {
        // 验证码配置参数
        $captchaConfig = $this->getConfigPlugin()->get('captcha');
        $captchaConfig['imgUrl'] = $this->getRequest()->getBaseUrl() . $captchaConfig['imgUrl'];


        $memberManager = $this->getSm(MemberManager::class);
        $weChatRemote = $this->getSm(Remote::class);

        $form = new ApplyForm($memberManager, $weChatRemote, $captchaConfig);


        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();


                echo '<pre>';
                print_r($data);
                print_r($_SESSION);
                echo '</pre>';
                //echo '<pre>';print_r($captchaConfig);echo '</pre>';

            }
        }


        return new ViewModel([
            'form' => $form,
        ]);
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
                $this->getAsyncRequestPlugin()->post($this->getServerPlugin()->domain() . $asyncUrl, $postData);

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
     */
    public function sendMailAction()
    {
        ignore_user_abort(true);
        set_time_limit(0);

        $subject = $this->params()->fromPost('mail_subject');
        $content = $this->params()->fromPost('mail_content');
        $recipient = $this->params()->fromPost('mail_recipient');

        if (empty($subject) || empty($content) || empty($recipient)) {
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '邮件发送参数不全');
            return $this->getResponse();
        }

        try {
            $this->getMailService()->sendMail($recipient, $subject, $content);
        } catch (InvalidArgumentException $e) {
            $this->getLoggerPlugin()->exception($e);
        } catch (RuntimeException $e) {
            $this->getLoggerPlugin()->exception($e);
        }

        return $this->getResponse();
    }
}
