<?php
/**
 * DepartmentController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Controller;


use Admin\Entity\Department;
use Admin\Entity\Member;
use Admin\Exception\RuntimeException;
use Admin\Form\DepartmentForm;
use Ramsey\Uuid\Uuid;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


/**
 * 系统分组管理
 *
 * Class DepartmentController
 * @package Admin\Controller
 */
class DepartmentController extends AdminBaseController
{

    /**
     * 分组列表
     */
    public function indexAction()
    {

        $viewHelperManager = $this->getSm('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        $page = (int)$this->params()->fromRoute('key', 1);
        if ($page < 1) {
            $page = 1;
        }

        $deptManager = $this->getDeptManager();

        $size = 10;
        $count = $deptManager->getAllDepartmentsCount();

        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/dept', ['action' => 'index', 'key' => '%d']));

        $rows = $deptManager->getAllDepartmentsByLimitPage($page, $size);

        /**
        foreach ($rows as $row) {
            if ($row instanceof Department) {
                $ms = $row->getMembers();
                $this->getLoggerPlugin()->debug('name: ' . $row->getDeptName() . ' members: ' . $ms->count());
                if ($ms->count()) {
                    foreach ($ms as $m) {
                        if ($m instanceof Member) {
                            $this->getLoggerPlugin()->debug('name: ' . $m->getMemberName());
                        }
                    }
                }

            }
        }
        //*/

        return new ViewModel([
            'rows' => $rows,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 新建分组
     */
    public function addAction()
    {
        $deptManager = $this->getDeptManager();

        $form = new DepartmentForm($deptManager);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $dept = new Department();
                $dept->setDeptId(Uuid::uuid1()->toString());
                $dept->setDeptName($data['name']);
                $dept->setDeptStatus(Department::STATUS_VALID);
                $dept->setDeptCreated(new \DateTime());

                $deptManager->saveModifiedEntity($dept);

                return $this->go(
                    '分组已经创建',
                    '新的分组: ' . $data['name'] . ' 已经创建成功!',
                    $this->url()->fromRoute('admin/dept')
                );
            }
        }

        return new ViewModel([
            'form' => $form,
            'activeId' => __METHOD__,
        ]);

    }


    /**
     * 启用/停用分组
     */
    public function statusAction()
    {
        $dept_id = $this->params()->fromRoute('key');
        if($dept_id == Department::DEFAULT_DEPT_ID) {
            throw new RuntimeException('禁止操作此数据');
        }

        $deptManager = $this->getDeptManager();
        $department = $deptManager->getDepartment($dept_id);

        if ($department->getDeptStatus() == Department::STATUS_VALID) { // to be invalid

            $department->setDeptStatus(Department::STATUS_INVALID);
            $members = $department->getMembers();
            if ($members->count()) {
                foreach ($members as $member) {
                    if ($member instanceof Member) {
                        $member->getDepts()->removeElement($department);
                    }
                }
            }

        } else { // to be valid
            $department->setDeptStatus(Department::STATUS_VALID);
        }

        $deptManager->saveModifiedEntity($department);

        return $this->go(
            '分组已更新',
            '分组: ' . $department->getDeptName() . ' 状态已经更新!',
            $this->url()->fromRoute('admin/dept')
        );
    }


    /**
     * 编辑分组信息
     */
    public function editAction()
    {
        $dept_id = $this->params()->fromRoute('key');
        if($dept_id == Department::DEFAULT_DEPT_ID) {
            throw new RuntimeException('禁止操作此数据');
        }

        $deptManager = $this->getDeptManager();
        $department = $deptManager->getDepartment($dept_id);

        $form = new DepartmentForm($deptManager, $department);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $department->setDeptName($data['name']);
                $deptManager->saveModifiedEntity($department);

                return $this->go(
                    '分组已更新',
                    '分组: ' . $data['name'] . ' 的信息已经更新!',
                    $this->url()->fromRoute('admin/dept')
                );
            }
        }

        return new ViewModel([
            'form' => $form,
            'dept' => $department,
            'activeId' => __CLASS__,
        ]);

    }


    /**
     * 查看分组成员列表
     */
    public function membersAction()
    {
        $dept_id = $this->params()->fromRoute('key');
        $dept = $this->getDeptManager()->getDepartment($dept_id);

        $data = [
            'inner' => [],
            'outer' => [],
            'dept' => $dept,
        ];

        $ownedMembers = $dept->getMembers();

        $members = $this->getMemberManager()->getMembers();
        foreach ($members as $member) {
            if ($member instanceof Member) {

                if (Member::DEFAULT_MEMBER_ID == $member->getMemberId()) {
                    continue;
                }

                if ($ownedMembers->contains($member)) {
                    array_push($data['inner'], $member);
                } else {
                    array_push($data['outer'], $member);
                }
            }
        }

        $viewModel = new ViewModel();
        $viewModel->setVariables(['data' => $data]);
        $viewModel->setTerminal(true);

        return $viewModel;
    }


    /**
     * 配置分组成员
     */
    public function updateMembersAction()
    {

        $json = ['success' => false];

        $dept_id = $this->params()->fromRoute('key');
        if ($dept_id == Department::DEFAULT_DEPT_ID) {
            throw new RuntimeException('禁止操作此数据');
        }

        if($this->getRequest()->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $deptManager = $this->getDeptManager();
            $dept = $deptManager->getDepartment($dept_id);

            $selected = (array)$this->params()->fromPost('selected');

            $members = $dept->getMembers();
            if ($members->count()) {
                foreach ($members as $member) {
                    if ($member instanceof Member) {
                        $member->getDepts()->removeElement($dept);
                    }
                }
            }

            foreach($selected as $id) {
                if ($id == Department::DEFAULT_DEPT_ID) {
                    continue;
                }

                $member = $this->getMemberManager()->getMember($id);
                if (null != $member) {
                    $member->getDepts()->add($dept);
                }
            }

            $deptManager->saveModifiedEntity($dept);

            $json['success'] = true;
        } else {

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
        $item = self::CreateControllerRegistry(__CLASS__, '系统分组管理', 'admin/dept', 1, 'users', 4);

        $item['actions']['index'] = self::CreateActionRegistry('index', '分组列表', 1, 'bars', 9);
        $item['actions']['add'] = self::CreateActionRegistry('add', '新建分组', 1, 'plus', 1);

        $item['actions']['status'] = self::CreateActionRegistry('status', '启用/停用分组');
        $item['actions']['edit'] = self::CreateActionRegistry('edit', '编辑分组信息');
        $item['actions']['members'] = self::CreateActionRegistry('members', '查看分组成员列表');
        $item['actions']['update-members'] = self::CreateActionRegistry('update-members', '配置分组成员');

        return $item;
    }

}