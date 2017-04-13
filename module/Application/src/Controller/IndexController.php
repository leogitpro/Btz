<?php
/**
 * IndexController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Application\Controller;

use Admin\Entity\Member;
use Admin\Exception\InvalidArgumentException as AdminInvalidArgumentException;
use Admin\Service\DepartmentManager;
use Admin\Service\MemberManager;
use Application\Exception\InvalidArgumentException;
use Application\Form\ApplyForm;
use Application\Form\ContactUsForm;
use Application\Form\TestForm;
use Mail\Exception\InvalidArgumentException as MailInvalidArgumentException;
use Mail\Exception\RuntimeException as MailRuntimeException;
use Ramsey\Uuid\Uuid;
use WeChat\Entity\Account;
use WeChat\Exception\RuntimeException as WeChatRuntimeException;
use WeChat\Exception\InvalidArgumentException as WeChatInvalidArgumentException;
use WeChat\Service\AccountService;
use WeChat\Service\NetworkService;
use Zend\View\Model\ViewModel;


class IndexController extends AppBaseController
{

    /**
     * Test page
     */
    public function testAction()
    {
        throw new InvalidArgumentException('test exception on application');
    }


    /**
     * 首页
     */
    public function indexAction()
    {
        //echo '<pre>';$v = $this->getConfigPlugin()->get('view_helpers');print_r($v);echo '</pre>';
        return new ViewModel();
    }

    /**
     * 产品&服务
     */
    public function serviceAction()
    {
        $items = [
            [
                'title' => '公众号接入',
                'desc' => '微信公众账号接入平台',
                'share' => 1,
                'pro' => 1,
            ],
            [
                'title' => '免费试用',
                'desc' => '每个微信认证公众号可以申请7天免费体验服务',
                'share' => 1,
                'pro' => 0,
            ],
            [
                'title' => '菜单管理',
                'desc' => '支持多套公众号自定义, 个性化菜单及切换服务',
                'share' => 1,
                'pro' => 1,
            ],
            [
                'title' => '接口管理',
                'desc' => 'AccessToken, 授权接口, JSSDK 分享接口服务',
                'share' => 1,
                'pro' => 1,
            ],
            [
                'title' => '接口安全管理',
                'desc' => '提供基于域名与 IP 及时效性接口访问控制服务',
                'share' => 1,
                'pro' => 1,
            ],
            [
                'title' => '二维码管理',
                'desc' => '任意生成 png, svg, eps 格式的公众号带参二维码',
                'share' => 1,
                'pro' => 1,
            ],
            [
                'title' => '二维码追踪服务',
                'desc' => '追踪由本平台发行的公众号二维码被扫描数据统计服务',
                'share' => 1,
                'pro' => 1,
            ],
            [
                'title' => '数据同步',
                'desc' => '公众号用户标签等数据同步服务',
                'share' => 1,
                'pro' => 1,
            ],
            [
                'title' => '接口访问统计',
                'desc' => '追踪授权出去的接口被调用数据统计',
                'share' => 0,
                'pro' => 1,
            ],
            [
                'title' => '超大并发请求服务',
                'desc' => '支持超大访问量的公众号接口调用',
                'share' => 0,
                'pro' => 1,
            ],
            [
                'title' => '独享服务器服务',
                'desc' => '支持公众号独立服务器部署支持',
                'share' => 0,
                'pro' => 1,
            ],
        ];

        return new ViewModel([
            'items' => [], //$items,
        ]);
    }

    /**
     * 用户账号激活
     */
    public function activeAction()
    {
        $activeCode = $this->params()->fromRoute('key', '');
        $memberManager = $this->getSm(MemberManager::class);

        try {
            $member = $memberManager->getMemberByActiveCode($activeCode);
            if($member->getMemberStatus() == Member::STATUS_RETRIED) {

                $deptManager = $this->getSm(DepartmentManager::class);

                $member->getDepts()->add($deptManager->getDefaultDepartment());
                $member->getDepts()->add($deptManager->getWeChatDepartment());

                $member->setMemberStatus(Member::STATUS_ACTIVATED);
                $member->setMemberActiveCode(md5(time() . rand(1111, 9999)));
                $memberManager->saveModifiedEntity($member);
            }

            return $this->getDisplayPlugin()->show(
                '账号激活成功!',
                '您的账号已经激活成功, 您可以登录管理平台进行更多的公众号管理.',
                $this->url()->fromRoute('admin'),
                '登入管理平台',
                5
            );

        } catch (AdminInvalidArgumentException $e) {
            return $this->getDisplayPlugin()->show(
                '无效的激活码!',
                '您的激活码可能已经被使用, 账号一经激活, 激活码即失效!',
                $this->url()->fromRoute('home'),
                '返回',
                3
            );
        }
    }


    /**
     * 申请试用
     */
    public function applyAction()
    {
        // 验证码配置参数
        $captchaConfig = $this->getConfigPlugin()->get('captcha');
        $captchaConfig['imgUrl'] = $this->getRequest()->getBaseUrl() . $captchaConfig['imgUrl'];


        $memberManager = $this->getSm(MemberManager::class);
        $accountService = $this->getSm(AccountService::class);

        $form = new ApplyForm($memberManager, $accountService, $captchaConfig);

        $error = '';

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $email = $data['email'];
                $name = $data['name'];
                $appId = $data['appid'];
                $appSecret = $data['appsecret'];

                try {
                    $res = NetworkService::getAccessToken($appId, $appSecret);

                    $password = uniqid();
                    $activeCode = md5($password . rand(1000, 9999));
                    $accountExpired = new \DateTime('next year');

                    $member = new Member();
                    $member->setMemberId(Uuid::uuid1()->toString());
                    $member->setMemberEmail($email);
                    $member->setMemberPassword(md5($password));
                    $member->setMemberName($name);
                    $member->setMemberLevel(Member::LEVEL_INTERIOR);
                    $member->setMemberStatus(Member::STATUS_RETRIED);
                    $member->setMemberActiveCode($activeCode);
                    $member->setMemberExpired($accountExpired);
                    $member->setMemberCreated(new \DateTime());

                    $weChatExpired = strtotime("+7 days");

                    $weChat = new Account();
                    $weChat->setWxAppId($appId);
                    $weChat->setWxAppSecret($appSecret);
                    $weChat->setWxChecked(Account::STATUS_CHECKED);
                    $weChat->setWxAccessToken($res['access_token']);
                    $weChat->setWxAccessTokenExpired(($res['expires_in'] + time() - 300));
                    $weChat->setWxExpired($weChatExpired);
                    $weChat->setWxCreated(new \DateTime());
                    $weChat->setMember($member);

                    $memberManager->saveModifiedEntities([$member, $weChat]);

                    // Send mail
                    $mailTpl = $this->getConfigPlugin()->get('mail.template.apply');

                    $active_url = $this->getServerPlugin()->domain() . $this->url()->fromRoute('active', ['key' => $activeCode, 'suffix' => '.html']);
                    $account_expired = $accountExpired->format('Y-m-d');
                    $wechat_expired = date('Y-m-d', $weChatExpired);
                    $contact_url = $this->getServerPlugin()->domain() . $this->url()->fromRoute('contact');

                    $mailTpl = str_replace('%username%', $name, $mailTpl);
                    $mailTpl = str_replace('%password%', $password, $mailTpl);

                    $mailTpl = str_replace('%active_url%', $active_url, $mailTpl);
                    $mailTpl = str_replace('%account_expired%', $account_expired, $mailTpl);
                    $mailTpl = str_replace('%wechat_expired%', $wechat_expired, $mailTpl);
                    $mailTpl = str_replace('%contact_url%', $contact_url, $mailTpl);

                    $postData = [
                        'mail_subject' => 'Btz微信接口平台试用通知',
                        'mail_content' => $mailTpl,
                        'mail_recipient' => $email,
                    ];

                    $asyncUrl = $this->url()->fromRoute('send-mail');
                    $this->getAsyncRequestPlugin()->post($this->getServerPlugin()->domain() . $asyncUrl, $postData);

                    return $this->getDisplayPlugin()->show(
                        '感谢您, 申请成功!',
                        '一封您的帐号激活邮件已经发往: ' . $email . ' 请检查邮件激活账号, 立即享用我们为您提供的专业服务吧!',
                        $this->url()->fromRoute('home'),
                        '返回',
                        5
                    );

                } catch (WeChatRuntimeException $e) {
                    $error = 'wx';
                } catch (WeChatInvalidArgumentException $e) {
                    $error = 'wx';
                }
            }
        }

        return new ViewModel([
            'form' => $form,
            'error' => $error,
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
        } catch (MailInvalidArgumentException $e) {
            $this->getLoggerPlugin()->exception($e);
        } catch (MailRuntimeException $e) {
            $this->getLoggerPlugin()->exception($e);
        }

        return $this->getResponse();
    }




}
