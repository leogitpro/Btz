<?php
/**
 * AuthController.php
 *
 * User Authentication
 *
 */

namespace User\Controller;


use Application\Service\MailManager;
use Doctrine\ORM\EntityManager;
use User\Entity\User;
use User\Form\ActiveForm;
use User\Form\ForgotPasswordForm;
use User\Form\LoginForm;
use User\Form\SignUpForm;
use User\Service\AuthManager;
use User\Service\UserManager;
use Zend\Authentication\AuthenticationService;
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
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var AuthManager
     */
    private $authManager;

    /**
     * @var AuthenticationService
     */
    private $authService;

    /**
     * @var UserManager
     */
    private $userManager;


    /**
     * AuthController constructor.
     *
     * @param EntityManager $entityManager
     * @param AuthManager $authManager
     * @param AuthenticationService $authService
     * @param UserManager $userManager
     */
    public function __construct(EntityManager $entityManager, AuthManager $authManager, AuthenticationService $authService,  UserManager $userManager)
    {
        $this->entityManager = $entityManager;
        $this->authManager = $authManager;
        $this->authService = $authService;
        $this->userManager = $userManager;
    }


    /**
     * Auto check user authentication and reload.
     */
    public function indexAction()
    {
        if($this->authService->hasIdentity()) {
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
        $form = new LoginForm();

        $isLoginError = false;

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) { // Validate form data
                $data = $form->getData(); // Get the filtered data
                $result = $this->authManager->login($data['email'], $data['password'], (int)$data['remember_me']);
                if ($result->getCode() == Result::SUCCESS) { // Check result.

                    return $this->display(
                        'Welcome',
                        'Thanks back to us!',
                        $this->url()->fromRoute('home'),
                        'Go home',
                        3
                    );

                } else {
                    $isLoginError = true;
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

        return $this->display(
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
        // Use sign up form
        $form = new SignUpForm($this->entityManager, null);

        // Post request check
        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) { // Validate form data

                $data = $form->getData(); // Get the filtered data

                $user = $this->userManager->addNewUser($data); // Save data to database

                // Show user profile url
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
            return ;
        }

        $user = $this->entityManager->getRepository(User::class)->find($uid);
        if (null == $user) {
            $this->getResponse()->setStatusCode(404);
            return ;
        }

        if (User::STATUS_ACTIVE == $user->getStatus()) { // Forbid resend active mail
            $this->getResponse()->setStatusCode(404);
            return ;
        }

        $activeUrl = $this->url()->fromRoute('user/auth_detail', [
            'action' => 'active',
            'key' => $user->getActiveToken(),
            'suffix' => '.html',
        ]);

        $msg = $this->config()->get('mail.template.active'); // Mail template
        $msg = str_replace('%username%', $user->getName(), $msg); // Fill username
        $msg = str_replace('%active_code%', $user->getActiveToken(), $msg); // Fill active code
        $msg = str_replace('%active_link%', $this->host()->getHost() . $activeUrl, $msg); // Fill active link

        $subject = 'Active your account';

        $serviceManager = $this->getEvent()->getApplication()->getServiceManager();
        $mailService = $serviceManager->get(MailManager::class);
        $mailService->sendMail($user->getEmail(), $subject, $msg);

        // Show sent page
        $toUrl = $this->url()->fromRoute('user/auth_detail', [
            'action' => 'sent-active-mail',
            'key' => $user->getUid(),
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

        return $this->display('Congratulations', $message, $resendActiveMailUrl, 'Resend active mail');

    }


    /**
     * Active a registered user
     *
     * @return ViewModel
     */
    public function activeAction()
    {
        $activeCode = $this->params()->fromRoute('key');

        $form = new ActiveForm($this->entityManager, $activeCode);

        if($this->getRequest()->isPost()) {

            $data = $this->params()->fromPost();
            $form->setData($data);

            if ($form->isValid()) {

                $data = $form->getData(); // Get the filtered and validated
                $user = $this->userManager->activeUser($data['active_code']);

                // Send mail to user
                if($user) {

                    $loginUrl = $this->url()->fromRoute('user/auth', ['suffix' => '.html']);

                    $msg = $this->config()->get('mail.template.activated'); // Mail template
                    $msg = str_replace('%username%', $user->getName(), $msg); // Fill username
                    $msg = str_replace('%login_link%', $this->host()->getHost() . $loginUrl, $msg); // Fill login link

                    $subject = 'Welcome ' . $user->getName();

                    $serviceManager = $this->getEvent()->getApplication()->getServiceManager();
                    $mailService = $serviceManager->get(MailManager::class);
                    $mailService->sendMail($user->getEmail(), $subject, $msg);
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
        return $this->display(
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
        $config = $this->config()->get('captcha');
        $config['imgUrl'] = $this->getRequest()->getBaseUrl() . $config['imgUrl'];
        $form = new ForgotPasswordForm($this->entityManager, $config);

        if($this->getRequest()->isPost()) {

            $data = $this->params()->fromPost();
            $form->setData($data);

            if ($form->isValid()) {

                $data = $form->getData(); // Get the filtered and validated
                $user = $this->userManager->resetUserPasswordToken($data['email']);

                // Send mail to user include the reset password link
                if($user) {

                    $resetUrl = $this->url()->fromRoute('user/auth_detail', [
                        'action' => 'reset-password',
                        'key' => $user->getPwdResetToken(),
                        'suffix' => '.html']);

                    $msg = $this->config()->get('mail.template.reset-password'); // Mail template
                    $msg = str_replace('%username%', $user->getName(), $msg); // Fill username
                    $msg = str_replace('%reset_link%', $this->host()->getHost() . $resetUrl, $msg); // Fill reset link
                    $msg = str_replace('%expired_hours%', 24, $msg); // Fill expired hours: 24

                    $subject = 'Reset password for ' . $user->getName();

                    $serviceManager = $this->getEvent()->getApplication()->getServiceManager();
                    $mailService = $serviceManager->get(MailManager::class);
                    $mailService->sendMail($user->getEmail(), $subject, $msg);
                }

                // Show success message
                return $this->display(
                    'Password reset',
                    'Hi, ' . $user->getName() . '! A reset password email has sent to your E-mail. Please check the mail and reset your new password. Thanks!'
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
        return new ViewModel();
    }

}