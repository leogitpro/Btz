<?php
/**
 * Administrator profile controller
 *
 * User: leo
 */

namespace Admin\Controller;


use Admin\Form\UpdatePasswordForm;
use Admin\Form\UpdateProfileForm;
use Admin\Service\AdminerManager;
use Admin\Service\AuthService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ProfileController extends AbstractActionController
{

    /**
     * Show administrator summary information page.
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $sm = $this->getEvent()->getApplication()->getServiceManager();

        $authService = $sm->get(AuthService::class);
        $adminerManager = $sm->get(AdminerManager::class);

        $adminer = $adminerManager->getAdministrator($authService->getIdentity());

        return new ViewModel(['adminer' => $adminer]);
    }


    /**
     * Update administrator password page.
     *
     * @return ViewModel
     */
    public function passwordAction()
    {
        $sm = $this->getEvent()->getApplication()->getServiceManager();

        $authService = $sm->get(AuthService::class);
        $adminerManager = $sm->get(AdminerManager::class);

        $form = new UpdatePasswordForm($adminerManager, $authService);
        if($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();

                $adminerManager->updateAdministratorPassword($authService->getIdentity(), md5($data['new_password']));
                $authService->clearIdentity();

                return $this->getMessagePlugin()->show(
                    'Password changed',
                    'Your password has been changed and need authenticate again. Please use the new password login.',
                    $this->url()->fromRoute('admin/index', ['suffix' => '.html']),
                    'Sign In',
                    3
                );
            }
        }

        return new ViewModel(['form' => $form]);

    }


    /**
     * Update administrator information page.
     *
     * @return ViewModel
     */
    public function updateAction()
    {
        $sm = $this->getEvent()->getApplication()->getServiceManager();

        $authService = $sm->get(AuthService::class);
        $adminerManager = $sm->get(AdminerManager::class);

        $adminer = $adminerManager->getAdministrator($authService->getIdentity());

        if (null == $adminer) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . ' Invalid administrator identity');
            return ;
        }

        $form = new UpdateProfileForm($adminer);

        if($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $adminer->setAdminName($data['name']);
                $adminerManager->saveUpdatedAdministrator($adminer);

                return $this->getMessagePlugin()->show(
                    'Profile updated',
                    'Your profile has been updated success!',
                    $this->url()->fromRoute('admin/profile'),
                    'My Profile',
                    1
                );
            }
        }

        return new ViewModel([
            'form' => $form,
        ]);

    }

}