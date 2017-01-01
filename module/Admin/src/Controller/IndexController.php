<?php
/**
 * Default controller
 */

namespace Admin\Controller;


use Admin\Form\LoginForm;
use Admin\Service\AuthManager;
use Admin\Service\AuthService;
use Zend\Authentication\Result;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;


class IndexController extends AbstractActionController
{

    /**
     * @var AuthService
     */
    private $authService;

    /**
     * @var AuthManager
     */
    private $authManager;



    public function onDispatch(MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();

        $this->authManager = $serviceManager->get(AuthManager::class);
        $this->authService = $serviceManager->get(AuthService::class);

        $response = parent::onDispatch($e);

        $viewModel = $e->getViewModel();
        $viewModel->setTemplate('layout/admin_simple');

        return $response;
    }


    /**
     * Auto switch router
     */
    public function indexAction()
    {
        if ($this->authService->hasIdentity()) {
            $this->redirect()->toRoute('admin/dashboard', ['suffix' => '.html']);
        } else {
            $this->redirect()->toRoute('admin/index', ['action' => 'login', 'suffix' => '.html']);
        }
    }


    /**
     * Administrator login
     *
     * @return ViewModel
     */
    public function loginAction()
    {
        $login_code = 1;

        $form = new LoginForm();

        if ($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if($form->isValid()) {

                $data = $form->getData();

                $result = $this->authManager->login($data['email'], md5($data['password']));

                if (Result::SUCCESS == $result->getCode()) {
                    return $this->getMessagePlugin()->show(
                        'Welcome',
                        'Thanks sign in control panel.',
                        $this->url()->fromRoute('admin'),
                        'Enter',
                        3
                    );
                } else {
                    $login_code = $result->getCode();
                }

            }
        }

        return new ViewModel(['form' => $form, 'login_code' => $login_code]);
    }


    /**
     * Administrator logout
     *
     * @return mixed
     */
    public function logoutAction()
    {
        $this->authManager->logout();

        return $this->getMessagePlugin()->show(
            'Identity cleaned',
            'The identity information has been cleaned safely. thanks sign in again!'
        );
    }


    /**
     * Display message
     *
     * @return ViewModel
     */
    public function messageAction()
    {
        $msg_title = $this->params()->fromRoute('title', 'Information');
        $msg_content = $this->params()->fromRoute('content', '...');
        $url_href = $this->params()->fromRoute('url_href', '#');
        $url_title = $this->params()->fromRoute('url_title', '');
        $delay = $this->params()->fromRoute('delay', 0);

        return new ViewModel([
            'msg_title' => $msg_title,
            'msg_content' => $msg_content,
            'url_href' => $url_href,
            'url_title' => $url_title,
            'delay' => $delay,
        ]);
    }
}