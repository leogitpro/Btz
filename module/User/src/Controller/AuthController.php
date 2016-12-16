<?php
/**
 * AuthController.php
 *
 * User Authentication
 *
 */

namespace User\Controller;


use Doctrine\ORM\EntityManager;
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
        return new ViewModel();
    }


    /**
     * Active a registed user
     *
     * @return ViewModel
     */
    public function activeAction()
    {
        $key = $this->params()->fromRoute('key');
        return new ViewModel(['key' => $key]);
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