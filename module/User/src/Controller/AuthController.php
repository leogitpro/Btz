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
use User\Form\SignUpForm;
use User\Service\AuthManager;
use User\Service\UserManager;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
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
        return new ViewModel();
    }

    /**
     * Destory user authenticationed data
     *
     */
    public function logoutAction()
    {
        //go to default page
        echo '<p>logout</p>';
        //auto go login or profile page
        return $this->getResponse();
    }


    /**
     * Create user account page
     *
     * @return ViewModel
     */
    public function signupAction()
    {

        $form = new SignUpForm($this->entityManager, null);

        if($this->getRequest()->isPost()) {

            $data = $this->params()->fromPost();
            $form->setData($data);

            if ($form->isValid()) {

                $data = $form->getData(); // Get the filtered and validated

                $user = $this->userManager->addNewUser($data);

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
     * Send active account mail
     *
     * @return \Zend\Stdlib\ResponseInterface
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


        $https = isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0;
        if (!$https) {
            $https =  isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1);
        }
        $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');

        $msg = 'Hi: ' . $user->getName() . PHP_EOL . PHP_EOL;
        $msg .= 'Please active your account by the follow code:' . PHP_EOL;
        $msg .= $user->getActiveToken() . PHP_EOL;
        $msg .= 'Or click the follow link address:' . PHP_EOL;
        $msg .= 'http' . ($https ? 's' : '') . '://' . $host . $activeUrl . PHP_EOL . PHP_EOL;
        $msg .= 'Thanks!';

        $subject = 'Active your account';

        $serviceManager = $this->getEvent()->getApplication()->getServiceManager();
        $mailService = $serviceManager->get(MailManager::class);
        $mailService->sendMail($user->getEmail(), $subject, $msg);

        // Show sended page
        $toUrl = $this->url()->fromRoute('user_auth_action_with_param', [
            'action' => 'sended-active-mail',
            'key' => $user->getUid(),
            'suffix' => '.html'
        ]);
        return $this->redirect()->toUrl($toUrl);
    }


    /**
     *
     * @return void|ViewModel
     */
    public function sendedActiveMailAction()
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

        if (User::STATUS_ACTIVE == $user->getStatus()) { // Forbid review send active mail
            $this->getResponse()->setStatusCode(404);
            return ;
        }

        $resendActiveMailUrl = $this->url()->fromRoute('user_auth_action_with_param', [
            'action' => 'send-active-mail',
            'key' => $user->getUid(),
            'suffix' => '.html'
        ]);


        return new ViewModel(['send_mail_url' => $resendActiveMailUrl]);

    }


    /**
     * Active a registed user
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


                    $https = isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0;
                    if (!$https) {
                        $https =  isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1);
                    }
                    $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');

                    $msg = 'Welcome: ' . $user->getName() . '!' . PHP_EOL . PHP_EOL;
                    $msg .= 'Thanks join us' . PHP_EOL;
                    $msg .= 'Click the follow link to qucik login:' . PHP_EOL;
                    $msg .= 'http' . ($https ? 's' : '') . '://' . $host . $loginUrl . PHP_EOL . PHP_EOL;
                    $msg .= 'Thanks!';

                    $subject = 'Welcome ' . $user->getName();

                    $serviceManager = $this->getEvent()->getApplication()->getServiceManager();
                    $mailService = $serviceManager->get(MailManager::class);
                    $mailService->sendMail($user->getEmail(), $subject, $msg);

                }

                // Show actived message
                $this->redirect()->toRoute('user_auth_actions', [
                    'action' => 'actived',
                    'suffix' => '.html',
                ]);
            }
        }

        return new ViewModel(['form' => $form]);
    }


    /**
     * Show actived page
     *
     * @return ViewModel
     */
    public function activedAction()
    {
        return new ViewModel();
    }


    /**
     * Get user forgot password page
     *
     * @return ViewModel
     */
    public function forgotPasswdAction()
    {
        return new ViewModel();
    }


    /**
     * User reset password page
     *
     * @return ViewModel
     */
    public function resetPasswdAction()
    {
        return new ViewModel();
    }

}