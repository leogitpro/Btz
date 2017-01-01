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
use Admin\Service\MemberManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class ProfileController extends AbstractActionController
{

    /**
     * @var AuthService
     */
    private $authService;

    /**
     * @var MemberManager
     */
    private $memberManager;


    public function onDispatch(MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();

        $this->authService = $serviceManager->get(AuthService::class);
        $this->memberManager = $serviceManager->get(MemberManager::class);

        return parent::onDispatch($e);
    }


    /**
     * Show administrator summary information page.
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $member = $this->memberManager->getMember($this->authService->getIdentity());

        return new ViewModel(['member' => $member]);
    }


    /**
     * Update administrator password page.
     *
     * @return ViewModel
     */
    public function passwordAction()
    {

        $form = new UpdatePasswordForm($this->memberManager, $this->authService);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $this->memberManager->updateMemberPassword($this->authService->getIdentity(), md5($data['new_password']));

                $this->authService->clearIdentity();

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

        $member = $this->memberManager->getMember($this->authService->getIdentity());
        if (null == $member) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . ' Invalid administrator identity');
            return ;
        }

        $form = new UpdateProfileForm($member);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $member->setMemberName($data['name']);
                $this->memberManager->saveModifiedMember($member);

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