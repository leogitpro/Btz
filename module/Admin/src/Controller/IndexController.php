<?php
/**
 * Default controller
 */

namespace Admin\Controller;


use Admin\Form\LoginForm;
use Admin\Service\AuthService;
use Zend\Authentication\Result;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;


class IndexController extends AdminBaseController
{
    public function onDispatch(MvcEvent $e)
    {
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
        if ($this->getSm(AuthService::class)->hasIdentity()) {
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

                $result = $this->getAuthManager()->login($data['email'], md5($data['password']));

                if (Result::SUCCESS == $result->getCode()) {
                    return $this->getMessagePlugin()->show(
                        '欢迎登入',
                        '欢迎你再次登入管理平台, 祝您发现更多惊喜!',
                        $this->url()->fromRoute('admin'),
                        '立即进入',
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
        $this->getAuthManager()->logout();

        return $this->getMessagePlugin()->show(
            '安全退出',
            '您的账号已经安全的退出系统, 下次登入你使用正确的账号和密码登入系统. 再见!'
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