<?php
/**
 * Administrator profile controller
 *
 * User: leo
 */

namespace Admin\Controller;


use Admin\Form\UpdatePasswordForm;
use Admin\Form\UpdateProfileForm;
use Admin\Service\MemberManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;


class ProfileController extends AbstractActionController
{
    /**
     * @var MemberManager
     */
    private $memberManager;


    public function onDispatch(MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();

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
        $member = $this->memberManager->getCurrentMember();
        if (null == $member) {
            $this->getResponse()->setStatusCode(404);
            return ;
        }

        return new ViewModel(['member' => $member]);
    }


    /**
     * Update administrator password.
     *
     * @return ViewModel
     */
    public function passwordAction()
    {
        $member = $this->memberManager->getCurrentMember();
        if (null == $member) {
            $this->getResponse()->setStatusCode(404);
            return ;
        }

        $form = new UpdatePasswordForm($this->memberManager);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $encryptedPassword = md5($data['new_password']); // Simple MD5 encrypt

                $member->setMemberPassword($encryptedPassword);
                $this->memberManager->saveModifiedEntity($member);

                return $this->getMessagePlugin()->show(
                    '密码已更新',
                    '您的密码已经更新, 请在下次使用新的密码登入!',
                    $this->url()->fromRoute('admin/profile', ['suffix' => '.html']),
                    '返回',
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

        $member = $this->memberManager->getCurrentMember();
        if (null == $member) {
            $this->getResponse()->setStatusCode(404);
            return ;
        }

        $form = new UpdateProfileForm($member);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $member->setMemberName($data['name']);

                $this->memberManager->saveModifiedEntity($member);

                return $this->getMessagePlugin()->show(
                    '资料已更新',
                    '您的个人资料已经更新!',
                    $this->url()->fromRoute('admin/profile'),
                    '返回',
                    1
                );
            }
        }

        return new ViewModel([
            'form' => $form,
            'member' => $member,
        ]);

    }

}