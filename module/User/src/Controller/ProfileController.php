<?php
/**
 * ProfileController.php
 *
 * User Profile manager
 *
 */

namespace User\Controller;


use Doctrine\ORM\EntityManager;
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
     * AuthController constructor.
     *
     * @param UserManager $userManager
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
            $this->getLoggerPlugin()->err(__METHOD__ . ' Invalid user identity');
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
        return new ViewModel();
    }


    /**
     * View a user profile
     *
     * @return ViewModel
     */
    public function viewAction()
    {
        $user_id = $this->params()->fromRoute('uid', 0);
        return new ViewModel(['user_id' => $user_id]);
    }

}