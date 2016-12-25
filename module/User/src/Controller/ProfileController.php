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
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ProfileController extends AbstractActionController
{

    /**
     * @var UserManager
     */
    private $userManager;


    /**
     * AuthController constructor.
     *
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Show current user profile
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        return new ViewModel();
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