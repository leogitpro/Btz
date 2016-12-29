<?php
/**
 * ProfileController.php
 *
 * User Profile manager
 * User: Leo
 */

namespace User\Controller;

use User\Form\UpdateEmailForm;
use User\Form\UpdatePasswordForm;
use User\Form\UpdateProfileForm;
use User\Service\AuthService;
use User\Service\UserManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ProfileController extends AbstractActionController
{

    /**
     * @var UserManager
     */
    private $userManager;


    /**
     * @var AuthService
     */
    private $authService;


    /**
     * ProfileController constructor.
     *
     * @param UserManager $userManager
     * @param AuthService $authService
     */
    public function __construct(UserManager $userManager, AuthService $authService)
    {
        $this->userManager = $userManager;
        $this->authService = $authService;
    }

    /**
     * Show current user profile
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $user = $this->userManager->getUserByEmail($this->authService->getIdentity());
        if (null == $user) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . ' Invalid user identity');
            return ;
        }

        return new ViewModel(['user' => $user]);
    }


    /**
     * User update profile page
     *
     * @return ViewModel
     */
    public function updateAction()
    {
        $user = $this->userManager->getUserByEmail($this->authService->getIdentity());
        if (null == $user) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . ' Invalid user identity');
            return ;
        }

        $form = new UpdateProfileForm($user);
        if($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $user->setName($data['name']); //Update user full name.
                $this->userManager->saveEditedUser($user);

                return $this->getDisplayPlugin()->show(
                    'Profile updated',
                    'Your profile has been updated success!',
                    $this->url()->fromRoute('user/profile', ['suffix' => '.html']),
                    'My Profile',
                    1
                );
            }
        }

        return new ViewModel([
            'form' => $form,
        ]);
    }


    /**
     * User update E-mail address page
     *
     * @return ViewModel
     */
    public function emailAction()
    {
        $user = $this->userManager->getUserByEmail($this->authService->getIdentity());
        if (null == $user) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Current user identity invalid!');
            return ;
        }

        $form = new UpdateEmailForm($this->userManager, $user);
        if($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $user = $this->userManager->updateUserEmail($user, $data['email']);
                $this->authService->clearIdentity();

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
                    'mail_subject' => 'Please active your account again',
                ];

                // Async send mail
                $asyncUrl = $this->url()->fromRoute('send-mail');
                $this->getLoggerPlugin()->debug("Start call async request:" . $asyncUrl);
                $this->getAsyncRequestPlugin()->post($this->getServerPlugin()->domain() . $asyncUrl, $postData);
                $this->getLoggerPlugin()->debug("Finished call async request");

                return $this->getDisplayPlugin()->show(
                    'E-mail address changed',
                    'Your E-mail address has been changed and need authenticate again. Please login.',
                    $this->url()->fromRoute('user/auth', ['suffix' => '.html']),
                    'Sign In',
                    3
                );
            }
        }

        return new ViewModel([
            'form' => $form,
        ]);
    }


    /**
     * User update password page
     *
     * @return ViewModel
     */
    public function passwordAction()
    {
        $form = new UpdatePasswordForm($this->userManager, $this->authService);
        if($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->userManager->updateUserPasswordByEmail($data['new_password'], $this->authService->getIdentity());
                $this->authService->clearIdentity();
                return $this->getDisplayPlugin()->show(
                    'Password changed',
                    'Your password has been changed and need authenticate again. Please use the new password login.',
                    $this->url()->fromRoute('user/auth', ['suffix' => '.html']),
                    'Sign In',
                    3
                );
            }
        }

        return new ViewModel(['form' => $form]);
    }


    /**
     * View a user profile
     *
     * @return ViewModel
     */
    public function viewAction()
    {
        $uid = (int)$this->params()->fromRoute('key', 0);
        $user = $this->userManager->getUserById($uid);
        if (null == $user) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . ' Invalid user id');
            return ;
        }

        return new ViewModel(['user' => $user]);
    }

}