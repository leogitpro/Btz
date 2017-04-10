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


/**
 * 个人信息管理
 *
 * Class ProfileController
 * @package Admin\Controller
 */
class ProfileController extends AdminBaseController
{

    /**
     * 个人资料
     */
    public function indexAction()
    {
        $member = $this->getMemberManager()->getCurrentMember();

        return new ViewModel(['member' => $member]);
    }

    /**
     * 个人密码
     *
     */
    public function passwordAction()
    {
        $memberManager = $this->getMemberManager();
        $member = $memberManager->getCurrentMember();

        $form = new UpdatePasswordForm($memberManager);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $encryptedPassword = md5($data['new_password']); // Simple MD5 encrypt

                $member->setMemberPassword($encryptedPassword);
                $memberManager->saveModifiedEntity($member);

                return $this->go(
                    '密码已更新',
                    '您的密码已经更新, 请在下次使用新的密码登入!',
                    $this->url()->fromRoute('admin/profile', ['suffix' => '.html'])
                );
            }
        }

        return new ViewModel(['form' => $form]);
    }


    /**
     * 个人资料
     */
    public function updateAction()
    {
        $memberManager = $this->getMemberManager();
        $member = $memberManager->getCurrentMember();

        $form = new UpdateProfileForm();

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $member->setMemberName($data['name']);

                $memberManager->saveModifiedEntity($member);

                return $this->go(
                    '资料已更新',
                    '您的个人资料已经更新!',
                    $this->url()->fromRoute('admin/profile')
                );
            }
        }

        return new ViewModel([
            'form' => $form,
            'member' => $member,
        ]);

    }

}