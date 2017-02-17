<?php
/**
 * MemberController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Controller;


use Admin\Entity\Department;
use Admin\Entity\Member;
use Admin\Form\MemberForm;
use Admin\Service\DepartmentManager;
use Admin\Service\MemberManager;
use Ramsey\Uuid\Uuid;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class MemberController extends BaseController
{

    /**
     * @var MemberManager
     */
    private $memberManager;

    /**
     * @var DepartmentManager
     */
    private $deptManager;


    public function onDispatch(MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();

        $this->memberManager = $serviceManager->get(MemberManager::class);
        $this->deptManager = $serviceManager->get(DepartmentManager::class);

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
                    'name' => '创建新成员',
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
                [
                    'action' => 'departments',
                    'name' => '查看成员部门'
                ],
                [
                    'action' => 'updateDepartments',
                    'name' => '分配成员部门'
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
     * Create new member
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

                $member = new Member();
                $member->setMemberId(Uuid::uuid1()->toString());
                $member->setMemberEmail($data['email']);
                $member->setMemberPassword($data['password']);
                $member->setMemberName($data['name']);
                $member->setMemberStatus(Member::STATUS_ACTIVATED);
                $member->setMemberLevel(Member::LEVEL_INTERIOR);
                $member->setMemberCreated(new \DateTime());

                $defaultDept = $this->deptManager->getDefaultDepartment(); // Add member to default group
                $member->getDepts()->add($defaultDept);

                $this->memberManager->saveModifiedEntity($member);

                return $this->getMessagePlugin()->show(
                    '成员已添加',
                    '新成员: ' . $data['name'] . ' 已经添加到系统中!',
                    $this->url()->fromRoute('admin/member'),
                    '查看成员列表',
                    3
                );
            }
        }

        return new ViewModel([
            'form' => $form,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * Edit status
     */
    public function statusAction()
    {
        $member_id = $this->params()->fromRoute('key', 0);
        if($member_id == Member::DEFAULT_MEMBER_ID) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '禁止修改超级管理员账号!');
            return ;
        }

        $member = $this->memberManager->getMember($member_id);
        if (null == $member) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '无效的成员编号: ' . $member_id);
            return ;
        }

        if ($member->getMemberStatus() == Member::STATUS_ACTIVATED) { // to be retried
            $member->setMemberStatus(Member::STATUS_RETRIED);
            $member->getDepts()->clear();
        } else { // to be activated, only restore with default department relation
            $member->getDepts()->add($this->deptManager->getDefaultDepartment());
            $member->setMemberStatus(Member::STATUS_ACTIVATED);
        }

        $this->memberManager->saveModifiedEntity($member);

        return $this->getMessagePlugin()->show(
            '成员状态已更新',
            '成员: ' . $member->getMemberName() . ' 的账号状态已经被更新!',
            $this->url()->fromRoute('admin/member'),
            '返回',
            3
        );
    }


    /**
     * Edit information
     *
     * @return ViewModel
     */
    public function editAction()
    {
        $member_id = $this->params()->fromRoute('key');
        if($member_id == Member::DEFAULT_MEMBER_ID) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '禁止修改超级管理员信息');
            return ;
        }

        $member = $this->memberManager->getMember($member_id);
        if (null == $member) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '无效的成员编号: ' . $member_id);
            return ;
        }

        $form = new MemberForm($this->memberManager, $member, ['email', 'name']);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $member->setMemberEmail($data['email']);
                $member->setMemberName($data['name']);

                $this->memberManager->saveModifiedEntity($member);

                return $this->getMessagePlugin()->show(
                    '成员信息已经更新',
                    '成员: ' . $data['name'] . ' 的账号信息已经被更新!',
                    $this->url()->fromRoute('admin/member'),
                    '返回',
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
        $member_id = $this->params()->fromRoute('key');
        if($member_id == Member::DEFAULT_MEMBER_ID) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '禁止修改超级管理员信息');
            return ;
        }

        $member = $this->memberManager->getMember($member_id);
        if (null == $member) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '无效的成员编号: ' . $member_id);
            return ;
        }

        $form = new MemberForm($this->memberManager, $member, ['level']);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $member->setMemberLevel($data['level']);

                $this->memberManager->saveModifiedEntity($member);

                return $this->getMessagePlugin()->show(
                    '成员等级已更新',
                    '成员: ' . $member->getMemberName() . ' 的等级信息已经被更新!',
                    $this->url()->fromRoute('admin/member'),
                    '返回',
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
     * Edit password.
     *
     * @return ViewModel
     */
    public function passwordAction()
    {
        $member_id = $this->params()->fromRoute('key');
        if($member_id == Member::DEFAULT_MEMBER_ID) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '禁止修改超级管理员信息');
            return ;
        }

        $member = $this->memberManager->getMember($member_id);
        if (null == $member) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '无效的成员编号: ' . $member_id);
            return ;
        }

        $form = new MemberForm($this->memberManager, $member, ['password']);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();
                $member->setMemberPassword(md5($data['password']));

                $this->memberManager->saveModifiedEntity($member);

                return $this->getMessagePlugin()->show(
                    '密码已更改',
                    '成员: ' . $member->getMemberName() . ' 的登入密码已经更新, 需要使用新密码登入!',
                    $this->url()->fromRoute('admin/member'),
                    '返回',
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
     * View a member with all departments relationship
     *
     * @return void|ViewModel
     */
    public function departmentsAction()
    {
        $member_id = $this->params()->fromRoute('key');
        $member = $this->memberManager->getMember($member_id);
        if (null == $member) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '无效的成员编号: ' . $member_id);
            return ;
        }

        $data = [
            'inner' => [],
            'outer' => [],
            'member' => $member,
        ];

        $ownedDepts = $member->getDepts();
        $departments = $this->deptManager->getDepartments();
        foreach ($departments as $department) {
            if ($department instanceof Department) {

                if (Department::DEFAULT_DEPT_ID == $department->getDeptId()) {
                    continue;
                }

                if ($ownedDepts->contains($department)) {
                    array_push($data['inner'], $department);
                } else {
                    array_push($data['outer'], $department);
                }
            }
        }

        $viewModel = new ViewModel();
        $viewModel->setVariables(['data' => $data]);
        $viewModel->setTerminal(true);
        return $viewModel;

    }


    /**
     * AJAX save member departments
     *
     * @return JsonModel
     */
    public function updateDepartmentsAction()
    {
        $json = ['success' => false];

        $member_id = $this->params()->fromRoute('key');
        if ($member_id == Member::DEFAULT_MEMBER_ID) {
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '禁止更新超级管理员信息');
            return new JsonModel($json);
        }

        if($this->getRequest()->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $member = $this->memberManager->getMember($member_id);
            if (null == $member) {
                $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '无效的成员编号:' . $member_id);
                return new JsonModel($json);
            }

            $selected = (array)$this->params()->fromPost('selected');

            $member->getDepts()->clear();
            foreach($selected as $id) {
                if ($id == Department::DEFAULT_DEPT_ID) {
                    continue;
                }
                $member->getDepts()->add($this->deptManager->getDepartment($id));
            }
            $member->getDepts()->add($this->deptManager->getDefaultDepartment());

            $this->memberManager->saveModifiedEntity($member);

            $json['success'] = true;
        } else {

        }

        return new JsonModel($json);
    }




}