<?php
/**
 * MemberController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Controller;


use Admin\Entity\Member;
use Admin\Form\MemberForm;
use Admin\Service\MemberManager;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;


class MemberController extends BaseController
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


    public function autoRegisterComponent()
    {
        return [
            'controller' => __CLASS__,
            'name' => '成员管理',
            'route' => 'admin/member',
            'menu' => true,
            'icon' => 'user',
            'rank' => 10,
            'actions' => [
                [
                    'action' => 'index',
                    'name' => '查看成员列表',
                    'icon' => 'bars',
                    'menu' => true,
                    'rank' => 9,
                ],
                [
                    'action' => 'add',
                    'name' => '创建成员',
                    'icon' => 'user-plus',
                    'menu' => true,
                    'rank' => 1,
                ],
                [
                    'action' => 'edit',
                    'name' => '修改成员资料',
                ],
                [
                    'action' => 'status',
                    'name' => '启用/禁用成员'
                ],
                [
                    'action' => 'level',
                    'name' => '修改成员等级'
                ],
                [
                    'action' => 'password',
                    'name' => '修改成员密码'
                ],
            ],
        ];
    }


    /**
     * Show administrator list page
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        // Page configuration
        $size = 10;
        $page = (int)$this->params()->fromRoute('key', 1);
        if ($page < 1) { $page = 1; }
        $count = $this->memberManager->getAllMembersCount();

        // Get pagination helper
        $viewHelperManager = $this->getEvent()->getApplication()->getServiceManager()->get('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/member', ['action' => 'index', 'key' => '%d']));

        // List data
        $rows = $this->memberManager->getAllMembersByLimitPage($page, $size);

        return new ViewModel([
            'rows' => $rows,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * Create new administrator
     *
     * @return ViewModel
     */
    public function addAction()
    {
        $form = new MemberForm($this->memberManager, null, ['email', 'password', 'name']);

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


    /**
     * Edit administrator page
     *
     * @return ViewModel
     */
    public function editAction()
    {
        $member_id = (int)$this->params()->fromRoute('key', 0);
        if($member_id == Member::DEFAULT_MEMBER_ID) {
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
            'activeId' => __CLASS__,
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
        if($member_id == Member::DEFAULT_MEMBER_ID) {
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
                $this->memberManager->updateMemberStatus($member, $data['status']);

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
            'activeId' => __CLASS__,
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
        if($member_id == Member::DEFAULT_MEMBER_ID) {
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
            'activeId' => __CLASS__,
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
        if($member_id == Member::DEFAULT_MEMBER_ID) {
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
            'activeId' => __CLASS__,
        ]);

    }

}