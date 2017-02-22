<?php
/**
 * Administrator profile controller
 *
 * User: leo
 */

namespace Admin\Controller;


use Admin\Form\UpdatePasswordForm;
use Admin\Form\UpdateProfileForm;
use Zend\View\Model\ViewModel;


class ProfileController extends AdminBaseController
{

    /**
     * Show administrator summary information page.
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $member = $this->getMemberManager()->getCurrentMember();
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
        $memberManager = $this->getMemberManager();
        $member = $memberManager->getCurrentMember();
        if (null == $member) {
            $this->getResponse()->setStatusCode(404);
            return ;
        }

        $form = new UpdatePasswordForm($memberManager);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $encryptedPassword = md5($data['new_password']); // Simple MD5 encrypt

                $member->setMemberPassword($encryptedPassword);
                $memberManager->saveModifiedEntity($member);

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
        $memberManager = $this->getMemberManager();
        $member = $memberManager->getCurrentMember();
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

                $memberManager->saveModifiedEntity($member);

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