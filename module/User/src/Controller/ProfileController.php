<?php
/**
 * ProfileController.php
 *
 * User Profile manager
 * User: Leo
 */

namespace User\Controller;

use User\Form\UpdatePasswordForm;
use User\Service\UserManager;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ProfileController extends AbstractActionController
{

    /**
     * @var UserManager
     */
    private $userManager;


    /**
     * @var AuthenticationService
     */
    private $authService;


    /**
     * ProfileController constructor.
     *
     * @param UserManager $userManager
     * @param AuthenticationService $authService
     */
    public function __construct(UserManager $userManager, AuthenticationService $authService)
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
        return new ViewModel();
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

                return $this->getDisplayPlugin()->show(
                    'Password updated',
                    'Your password has been changed. Please use the new password login at the next time.',
                    $this->url()->fromRoute('user/profile', ['suffix' => '.html']),
                    'View my profile',
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