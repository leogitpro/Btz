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
        echo '<p>authentication index</p>';
        //auto go login or profile page
        return $this->getResponse();
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
                    return $this->redirect()->toRoute('home');
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
        return $this->redirect()->toRoute('home');
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
                $toUrl = $this->url()->fromRoute('user_auth_action_with_param', [
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

        $activeUrl = $this->url()->fromRoute('user_auth_action_with_param', [
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
        $toUrl = $this->url()->fromRoute('user_auth_action_with_param', [
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
        $resendActiveMailUrl = $this->url()->fromRoute('user_auth_action_with_param', [
            'action' => 'send-active-mail',
            'key' => $uid,
            'suffix' => '.html'
        ]);

        return new ViewModel(['send_mail_url' => $resendActiveMailUrl]);

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

                    $loginUrl = $this->url()->fromRoute('user_auth_actions', [
                        'action' => 'login',
                        'suffix' => '.html',
                    ]);

                    $msg = $this->config()->get('mail.template.activated'); // Mail template
                    $msg = str_replace('%username%', $user->getName(), $msg); // Fill username
                    $msg = str_replace('%login_link%', $this->host()->getHost() . $loginUrl, $msg); // Fill login link

                    $subject = 'Welcome ' . $user->getName();

                    $serviceManager = $this->getEvent()->getApplication()->getServiceManager();
                    $mailService = $serviceManager->get(MailManager::class);
                    $mailService->sendMail($user->getEmail(), $subject, $msg);
                }

                // Show activated message
                $this->redirect()->toRoute('user_auth_actions', [
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
        return new ViewModel();
    }



    /**
     * Get user forgot password page
     *
     * @return ViewModel
     */
    public function forgotPasswordAction()
    {
        return new ViewModel();
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