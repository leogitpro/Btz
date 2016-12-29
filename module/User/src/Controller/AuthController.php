<?php
/**
 * AuthController.php
 *
 * User Authentication
 * User: Leo
 */

namespace User\Controller;


use User\Entity\User;
use User\Form\ActiveForm;
use User\Form\ForgotPasswordForm;
use User\Form\LoginForm;
use User\Form\ResetPasswordForm;
use User\Form\SignUpForm;
use User\Service\AuthManager;
use User\Service\AuthService;
use User\Service\UserManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Authentication\Result;
use Zend\View\Model\ViewModel;


/**
 * Class AuthController
 *
 * @package User\Controller
 */
class AuthController extends AbstractActionController
{

    /**
     * @var AuthManager
     */
    private $authManager;

    /**
     * @var AuthService
     */
    private $authService;

    /**
     * @var UserManager
     */
    private $userManager;


    /**
     * AuthController constructor.
     *
     * @param AuthManager $authManager
     * @param AuthService $authService
     * @param UserManager $userManager
     */
    public function __construct(AuthManager $authManager, AuthService $authService, UserManager $userManager)
    {
        $this->authManager = $authManager;
        $this->authService = $authService;
        $this->userManager = $userManager;
    }


    /**
     * Auto check user authentication and reload.
     */
    public function indexAction()
    {
        if ($this->authService->hasIdentity()) {
            $this->redirect()->toRoute('home');
        } else {
            $this->redirect()->toRoute('user/auth', ['action' => 'login', 'suffix' => '.html']);
        }
    }


    /**
     * User authentication page
     *
     * @return ViewModel
     */
    public function loginAction()
    {
        if ($this->authService->hasIdentity()) { // Forbid Re-login
            $this->redirect()->toRoute('home');
            return false;
        }

        $form = new LoginForm();
        $isLoginError = 0;

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) { // Validate form data
                $data = $form->getData(); // Get the filtered data

                $result = $this->authManager->login($data['email'], $data['password'], (int)$data['remember_me']);

                if ($result->getCode() == Result::SUCCESS) { // Check result.

                    return $this->getDisplayPlugin()->show(
                        'Welcome',
                        'Thanks back to us!',
                        $this->url()->fromRoute('home'),
                        'Go home',
                        3
                    );

                } else {
                    if(Result::FAILURE == $result->getCode()) {
                        $isLoginError = 2;
                    } else {
                        $isLoginError = 1;
                    }
                }
            }
        }

        return new ViewModel([
            'isLoginError' => $isLoginError,
            'form' => $form,
        ]);
    }

    /**
     * Clean user login data
     *
     */
    public function logoutAction()
    {
        $this->authManager->logout();

        return $this->getDisplayPlugin()->show(
            'Identity cleaned',
            'The identity information has been cleaned safely. thanks sign in again!',
            $this->url()->fromRoute('home'),
            'Go home',
            3
        );
    }


    /**
     * Create user account page
     *
     * @return ViewModel
     */
    public function signUpAction()
    {
        $form = new SignUpForm($this->userManager, null);

        if ($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) { // Validate form data

                $data = $form->getData(); // Get the filtered data
                $user = $this->userManager->addNewUser($data); // Save data to database

                $toUrl = $this->url()->fromRoute('user/auth_detail', [
                    'action' => 'send-active-mail',
                    'key' => $user->getUid(),
                    'suffix' => '.html'
                ]);

                $this->redirect()->toUrl($toUrl);
            }
        }

        return new ViewModel([
            'form' => $form,
        ]);
    }


    /**
     * Send active mail to registered user
     */
    public function sendActiveMailAction()
    {
        $uid = (int)$this->params()->fromRoute('key', 0);
        if ($uid < 1) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . ' Invalid uid for send active mail');
            return ;
        }

        $user = $this->userManager->getUserById($uid);
        if (null == $user) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . ' Invalid uid['.$uid.'] for send active mail. No user information exists.');
            return ;
        }

        if (User::STATUS_ACTIVE == $user->getStatus()) { // Forbid resend active mail
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . ' Invalid uid['.$uid.'] is activated. Forbid re-active user.');
            return ;
        }

        $activeUrl = $this->url()->fromRoute('user/auth_detail', [
            'action' => 'active',
            'key' => $user->getActiveToken(),
            'suffix' => '.html',
        ]);
        $msg = $this->getConfigPlugin()->get('mail.template.active'); // Mail template
        $msg = str_replace('%username%', $user->getName(), $msg); // Fill username
        $msg = str_replace('%active_code%', $user->getActiveToken(), $msg); // Fill active code
        $msg = str_replace('%active_link%', $this->getServerPlugin()->domain() . $activeUrl, $msg); // Fill active link

        $postData = [
            'mail_content' =>  $msg,
            'mail_recipient' => $user->getEmail(),
            'mail_subject' => 'Please active your account',
        ];

        $asyncUrl = $this->url()->fromRoute('send-mail');
        $this->getLoggerPlugin()->debug("Start call async request:" . $asyncUrl);
        $this->getAsyncRequestPlugin()->post($this->getServerPlugin()->domain() . $asyncUrl, $postData);
        $this->getLoggerPlugin()->debug("Finished call async request");

        // Show sent page
        $toUrl = $this->url()->fromRoute('user/auth_detail', [
            'action' => 'sent-active-mail',
            'key' => $uid,
            'suffix' => '.html'
        ]);
        return $this->redirect()->toUrl($toUrl);
    }

    /**
     * Show sent mail page
     *
     * @return ViewModel
     */
    public function sentActiveMailAction()
    {

        $uid = (int)$this->params()->fromRoute('key', 0);
        $resendActiveMailUrl = $this->url()->fromRoute('user/auth_detail', [
            'action' => 'send-active-mail',
            'key' => $uid,
            'suffix' => '.html'
        ]);

        $message = 'A active mail has sent to your sign up email box.<br>';
        $message .= 'Please check and active your account.<br>';
        $message .= 'Thanks<br>';
        $message .= 'If you haven\'t received the mail. Pls use the follow button.';

        return $this->getDisplayPlugin()->show('Congratulations', $message, $resendActiveMailUrl, 'Resend active mail');

    }


    /**
     * Active a registered user
     *
     * @return ViewModel
     */
    public function activeAction()
    {
        $activeCode = $this->params()->fromRoute('key');
        $form = new ActiveForm($this->userManager, $activeCode);

        if($this->getRequest()->isPost()) {

            $data = $this->params()->fromPost();
            $form->setData($data);

            if ($form->isValid()) {

                $data = $form->getData(); // Get the filtered and validated
                $user = $this->userManager->activeUser($data['active_code']);

                if($user) { // Send mail to user
                    $loginUrl = $this->url()->fromRoute('user/auth', ['suffix' => '.html']);
                    $msg = $this->getConfigPlugin()->get('mail.template.activated');
                    $msg = str_replace('%username%', $user->getName(), $msg);
                    $msg = str_replace('%login_link%', $this->getServerPlugin()->domain() . $loginUrl, $msg);

                    $postData = [
                        'mail_subject' => 'Welcome ' . $user->getName(),
                        'mail_content' => $msg,
                        'mail_recipient' => $user->getEmail(),
                    ];

                    $asyncUrl = $this->url()->fromRoute('send-mail');
                    $this->getLoggerPlugin()->debug("Start call async request:" . $asyncUrl);
                    $this->getAsyncRequestPlugin()->post($this->getServerPlugin()->domain() . $asyncUrl, $postData);
                    $this->getLoggerPlugin()->debug("Finished call async request");
                }

                // Show activated message
                $this->redirect()->toRoute('user/auth', [
                    'action' => 'activated',
                    'suffix' => '.html',
                ]);
            }
        }

        return new ViewModel(['form' => $form]);
    }

    /**
     * Show activated page
     *
     * @return ViewModel
     */
    public function activatedAction()
    {
        return $this->getDisplayPlugin()->show(
            'Congratulations',
            'Your account is activated. Use the follow button quick sign in.',
            $this->url()->fromRoute('user/auth', ['action' => 'login', 'suffix' => '.html']),
            'Sign In'
        );
    }



    /**
     * Get user forgot password page
     *
     * @return ViewModel
     */
    public function forgotPasswordAction()
    {
        $config = $this->getConfigPlugin()->get('captcha');
        $config['imgUrl'] = $this->getRequest()->getBaseUrl() . $config['imgUrl'];
        $form = new ForgotPasswordForm($this->userManager, $config);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData(); // Get the filtered and validated
                $user = $this->userManager->resetUserPasswordToken($data['email']);

                if($user) { // Send mail to user include the reset password link

                    $resetUrl = $this->url()->fromRoute('user/auth_detail', [
                        'action' => 'reset-password',
                        'key' => $user->getPwdResetToken(),
                        'suffix' => '.html']);
                    $expired = $this->getConfigPlugin()->get('user.auth.reset_password_expired', 24);
                    $msg = $this->getConfigPlugin()->get('mail.template.reset-password');
                    $msg = str_replace('%username%', $user->getName(), $msg);
                    $msg = str_replace('%reset_link%', $this->getServerPlugin()->domain() . $resetUrl, $msg);
                    $msg = str_replace('%expired_hours%', $expired, $msg);

                    $postData = [
                        'mail_subject' => 'Reset password for ' . $user->getName(),
                        'mail_content' => $msg,
                        'mail_recipient' => $user->getEmail(),
                    ];

                    $asyncUrl = $this->url()->fromRoute('send-mail');
                    $this->getLoggerPlugin()->debug("Start call async request:" . $asyncUrl);
                    $this->getAsyncRequestPlugin()->post($this->getServerPlugin()->domain() . $asyncUrl, $postData);
                    $this->getLoggerPlugin()->debug("Finished call async request");
                }

                // Show success message
                return $this->getDisplayPlugin()->show(
                    'Password reset',
                    'There is a reset password email has sent to your E-mail. Please check the mail and reset your new password. Thanks!'
                );
            }
        }

        return new ViewModel([
            'form' => $form,
        ]);
    }


    /**
     * User reset password page
     *
     * @return ViewModel
     */
    public function resetPasswordAction()
    {

        $resetPwdCode = $this->params()->fromRoute('key');
        $user = $this->userManager->getUserByResetPwdToken($resetPwdCode);
        if (null == $user) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . ' Invalid reset code for password reset');
            return ;
        }

        $expired = $this->getConfigPlugin()->get('user.auth.reset_password_expired', 24);
        $expired = $expired * 3600;

        $nowTime = time();

        $requestTime = $user->getPwdResetTokenCreated();
        if(($requestTime + $expired) < $nowTime) {
            return $this->getDisplayPlugin()->show(
                'Request expired',
                'The requested reset password is expired!'
            );
        }

        $form = new ResetPasswordForm();
        if($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->userManager->resetPasswordByToken($resetPwdCode, $data['password']);
                return $this->getDisplayPlugin()->show(
                    'Password updated',
                    'Your password has been updated. Please use new password to sign in.'
                );
            }
        }

        return new ViewModel([
            'form' => $form,
        ]);
    }

}