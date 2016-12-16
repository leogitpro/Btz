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
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var UserManager
     */
    private $userManager;


    /**
     * AuthController constructor.
     * @param EntityManager $entityManager
     * @param UserManager $userManager
     */
    public function __construct(EntityManager $entityManager, UserManager $userManager)
    {
        $this->entityManager = $entityManager;
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