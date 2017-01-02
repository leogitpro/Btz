<?php
/**
 * MemberController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Controller;


use Admin\Form\MemberForm;
use Admin\Service\MemberManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class MemberController extends AbstractActionController
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


    public function indexAction()
    {

        $rows = $this->memberManager->getAllMembers();

        return new ViewModel([
            'rows' => $rows,
        ]);
    }


    /**
     * Edit administrator page
     *
     * @return ViewModel
     */
    public function editAction()
    {
        $member_id = (int)$this->params()->fromRoute('key', 0);
        if($member_id <= 1) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Forbid edit root administrator');
            return ;
        }

        $member = $this->memberManager->getMember($member_id);
        if (null == $member) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . ' Invalid member id: ' . $member_id);
            return ;
        }

        $form = new MemberForm($this->memberManager, $member, ['email', 'name']);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $member->setMemberEmail($data['email']);
                $member->setMemberName($data['name']);

                $this->memberManager->saveModifiedMember($member);

                return $this->getMessagePlugin()->show(
                    'Administrator updated',
                    'The administrator: ' . $data['name'] . ' has been updated success!',
                    $this->url()->fromRoute('admin/member'),
                    'Members',
                    3
                );
            }
        }

        return new ViewModel([
            'form' => $form,
            'member' => $member,
        ]);

    }


    /**
     * Edit administrator status page
     *
     * @return ViewModel
     */
    public function statusAction()
    {
        $member_id = (int)$this->params()->fromRoute('key', 0);
        if($member_id <= 1) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Forbid edit root administrator');
            return ;
        }

        $member = $this->memberManager->getMember($member_id);
        if (null == $member) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . ' Invalid member id: ' . $member_id);
            return ;
        }

        $form = new MemberForm($this->memberManager, $member, ['status']);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $member->setMemberStatus($data['status']);

                $this->memberManager->saveModifiedMember($member);

                return $this->getMessagePlugin()->show(
                    'Administrator status updated',
                    'The administrator: ' . $member->getMemberName() . ' status has been updated success!',
                    $this->url()->fromRoute('admin/member'),
                    'Members',
                    3
                );
            }
        }

        return new ViewModel([
            'form' => $form,
            'member' => $member,
        ]);
    }


    /**
     * Edit level page
     *
     * @return ViewModel
     */
    public function levelAction()
    {
        $member_id = (int)$this->params()->fromRoute('key', 0);
        if($member_id <= 1) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Forbid edit root administrator');
            return ;
        }

        $member = $this->memberManager->getMember($member_id);
        if (null == $member) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . ' Invalid member id: ' . $member_id);
            return ;
        }

        $form = new MemberForm($this->memberManager, $member, ['level']);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $member->setMemberLevel($data['level']);

                $this->memberManager->saveModifiedMember($member);

                return $this->getMessagePlugin()->show(
                    'Administrator level updated',
                    'The administrator: ' . $member->getMemberName() . ' level has been updated success!',
                    $this->url()->fromRoute('admin/member'),
                    'Members',
                    300
                );
            }
        }

        return new ViewModel([
            'form' => $form,
            'member' => $member,
        ]);

    }


    /**
     * Edit administrator password.
     *
     * @return ViewModel
     */
    public function passwordAction()
    {
        $member_id = (int)$this->params()->fromRoute('key', 0);
        if($member_id <= 1) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Forbid edit root administrator');
            return ;
        }

        $member = $this->memberManager->getMember($member_id);
        if (null == $member) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . ' Invalid member id: ' . $member_id);
            return ;
        }

        $form = new MemberForm($this->memberManager, $member, ['password']);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();
                $member->setMemberPassword(md5($data['password']));

                $this->memberManager->saveModifiedMember($member);

                return $this->getMessagePlugin()->show(
                    'Administrator password updated',
                    'The administrator: ' . $member->getMemberName() . ' password has been updated success!',
                    $this->url()->fromRoute('admin/member'),
                    'Members',
                    3
                );
            }
        }

        return new ViewModel([
            'form' => $form,
            'member' => $member,
        ]);

    }


    /**
     * Create new administrator
     *
     * @return ViewModel
     */
    public function addAction()
    {
        $form = new MemberForm($this->memberManager);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();
                $data['password'] = md5($data['password']);

                $this->memberManager->createMember($data);

                return $this->getMessagePlugin()->show(
                    'Administrator added',
                    'The new administrator: ' . $data['name'] . ' has been created success!',
                    $this->url()->fromRoute('admin/member'),
                    'Members',
                    3
                );
            }
        }

        return new ViewModel([
            'form' => $form,
        ]);
    }

}