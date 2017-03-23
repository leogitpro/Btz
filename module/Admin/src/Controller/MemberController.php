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
use Admin\Exception\RuntimeException;
use Admin\Form\MemberForm;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


/**
 * 系统成员管理
 *
 * Class MemberController
 * @package Admin\Controller
 */
class MemberController extends AdminBaseController
{

    /**
     * 系统成员列表
     */
    public function indexAction()
    {
        // Page configuration
        $size = 10;
        $page = (int)$this->params()->fromRoute('key', 1);
        if ($page < 1) { $page = 1; }

        $memberManager = $this->getMemberManager();
        $count = $memberManager->getAllMembersCount();

        // Get pagination helper
        $viewHelperManager = $this->getSm('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/member', ['action' => 'index', 'key' => '%d']));

        // List data
        $rows = $memberManager->getAllMembersByLimitPage($page, $size);

        return new ViewModel([
            'rows' => $rows,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 新增系统成员
     */
    public function addAction()
    {
        $memberManager = $this->getMemberManager();

        $form = new MemberForm($memberManager, null, ['email', 'password', 'name']);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();
                $data['password'] = md5($data['password']);

                $defaultDept = $this->getDeptManager()->getDefaultDepartment(); // Add member to default group
                $memberManager->createMember($data['email'], $data['password'], $data['name'], [$defaultDept]);

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
     * 启用/停用成员账号
     */
    public function statusAction()
    {
        $member_id = $this->params()->fromRoute('key', 0);
        if($member_id == Member::DEFAULT_MEMBER_ID) {
            throw new RuntimeException('禁止操作该用户帐号');
        }

        $memberManager = $this->getMemberManager();
        $member = $memberManager->getMember($member_id);

        if ($member->getMemberStatus() == Member::STATUS_ACTIVATED) { // to be retried
            $member->setMemberStatus(Member::STATUS_RETRIED);
            $member->getDepts()->clear();
        } else { // to be activated, only restore with default department relation
            $member->getDepts()->add($this->getDeptManager()->getDefaultDepartment());
            $member->setMemberStatus(Member::STATUS_ACTIVATED);
        }

        $memberManager->saveModifiedEntity($member);

        return $this->go(
            '成员状态已更新',
            '成员: ' . $member->getMemberName() . ' 的账号状态已经被更新!',
            $this->url()->fromRoute('admin/member')
        );
    }


    /**
     * 修改成员信息
     */
    public function editAction()
    {
        $member_id = $this->params()->fromRoute('key');
        if($member_id == Member::DEFAULT_MEMBER_ID) {
            throw new RuntimeException('禁止操作该用户帐号');
        }

        $memberManager = $this->getMemberManager();
        $member = $memberManager->getMember($member_id);

        $form = new MemberForm($memberManager, $member, ['email', 'name']);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $member->setMemberEmail($data['email']);
                $member->setMemberName($data['name']);

                $memberManager->saveModifiedEntity($member);

                return $this->go(
                    '成员信息已经更新',
                    '成员: ' . $data['name'] . ' 的账号信息已经被更新!',
                    $this->url()->fromRoute('admin/member')
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
     * 修改成员等级
     */
    public function levelAction()
    {
        $member_id = $this->params()->fromRoute('key');
        if($member_id == Member::DEFAULT_MEMBER_ID) {
            throw new RuntimeException('禁止操作该用户帐号');
        }

        $memberManager = $this->getMemberManager();
        $member = $memberManager->getMember($member_id);

        $form = new MemberForm($memberManager, $member, ['level']);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $member->setMemberLevel($data['level']);

                $memberManager->saveModifiedEntity($member);

                return $this->go(
                    '成员等级已更新',
                    '成员: ' . $member->getMemberName() . ' 的等级信息已经被更新!',
                    $this->url()->fromRoute('admin/member')
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
     * 修改成员密码
     */
    public function passwordAction()
    {
        $member_id = $this->params()->fromRoute('key');
        if($member_id == Member::DEFAULT_MEMBER_ID) {
            throw new RuntimeException('禁止操作该用户帐号');
        }

        $memberManager = $this->getMemberManager();
        $member = $memberManager->getMember($member_id);

        $form = new MemberForm($memberManager, $member, ['password']);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();
                $member->setMemberPassword(md5($data['password']));

                $memberManager->saveModifiedEntity($member);

                return $this->go(
                    '密码已更改',
                    '成员: ' . $member->getMemberName() . ' 的登入密码已经更新, 需要使用新密码登入!',
                    $this->url()->fromRoute('admin/member')
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
     * 修改成员过期时间
     */
    public function expiredAction()
    {
        $member_id = $this->params()->fromRoute('key');
        if($member_id == Member::DEFAULT_MEMBER_ID) {
            throw new RuntimeException('禁止操作该用户帐号');
        }

        $memberManager = $this->getMemberManager();
        $member = $memberManager->getMember($member_id);

        $form = new MemberForm($memberManager, $member, ['expired']);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $dt = new \DateTime($data['expired']);
                $member->setMemberExpired($dt);

                $memberManager->saveModifiedEntity($member);

                return $this->go(
                    '日期已更改',
                    '成员: ' . $member->getMemberName() . ' 的账号过期时间已经更新!',
                    $this->url()->fromRoute('admin/member')
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
     * 查看成员所属分组
     */
    public function departmentsAction()
    {
        $member_id = $this->params()->fromRoute('key');
        $member = $this->getMemberManager()->getMember($member_id);

        $data = [
            'inner' => [],
            'outer' => [],
            'member' => $member,
        ];

        $ownedDepts = $member->getDepts();
        $departments = $this->getDeptManager()->getDepartments();
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
     * 配置成员分组
     */
    public function updateDepartmentsAction()
    {
        $json = ['success' => false];

        $member_id = $this->params()->fromRoute('key');
        if ($member_id == Member::DEFAULT_MEMBER_ID) {
            throw new RuntimeException('禁止操作该用户帐号');
        }

        if($this->getRequest()->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $member = $this->getMemberManager()->getMember($member_id);

            $selected = (array)$this->params()->fromPost('selected');

            $member->getDepts()->clear();
            foreach($selected as $id) {
                if ($id == Department::DEFAULT_DEPT_ID) {
                    continue;
                }
                $member->getDepts()->add($this->getDeptManager()->getDepartment($id));
            }
            $member->getDepts()->add($this->getDeptManager()->getDefaultDepartment());

            $this->getMemberManager()->saveModifiedEntity($member);

            $json['success'] = true;
        }

        return new JsonModel($json);
    }


    /**
     *  ACL 登记
     *
     * @return array
     */
    public static function ComponentRegistry()
    {
        $item = self::CreateControllerRegistry(__CLASS__, '系统成员管理', 'admin/member', 1, 'user', 10);

        $item['actions']['index'] = self::CreateActionRegistry('index', '系统成员列表', 1, 'bars', 9);
        $item['actions']['add'] = self::CreateActionRegistry('add', '新增系统成员', 1, 'user-plus', 1);

        $item['actions']['status'] = self::CreateActionRegistry('status', '启用/停用成员账号');
        $item['actions']['edit'] = self::CreateActionRegistry('edit', '修改成员信息');
        $item['actions']['level'] = self::CreateActionRegistry('level', '修改成员等级');
        $item['actions']['password'] = self::CreateActionRegistry('password', '修改成员密码');
        $item['actions']['expired'] = self::CreateActionRegistry('expired', '修改成员过期时间');
        $item['actions']['departments'] = self::CreateActionRegistry('departments', '查看成员所属分组');
        $item['actions']['update-departments'] = self::CreateActionRegistry('update-departments', '配置成员分组');

        return $item;
    }


}