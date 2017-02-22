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
use Admin\Form\DepartmentForm;
use Ramsey\Uuid\Uuid;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class DepartmentController extends AdminBaseController
{

    /**
     * List departments
     *
     * @return ViewModel
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
     * Create department page
     *
     * @return ViewModel
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

                return $this->getMessagePlugin()->show(
                    '部门已经创建',
                    '新的部门: ' . $data['name'] . ' 已经创建成功!',
                    $this->url()->fromRoute('admin/dept'),
                    '查看部门列表',
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
     * Setting department status
     */
    public function statusAction()
    {
        $dept_id = $this->params()->fromRoute('key');
        if($dept_id == Department::DEFAULT_DEPT_ID) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '禁止更新基础部门');
            return ;
        }

        $deptManager = $this->getDeptManager();
        $department = $deptManager->getDepartment($dept_id);
        if (null == $department) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '无效的部门ID: ' . $dept_id);
            return ;
        }

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

        return $this->getMessagePlugin()->show(
            '部门已更新',
            '部门: ' . $department->getDeptName() . ' 状态已经更新!',
            $this->url()->fromRoute('admin/dept'),
            '返回',
            3
        );
    }


    /**
     * Edit department information page
     *
     * @return ViewModel
     */
    public function editAction()
    {
        $dept_id = $this->params()->fromRoute('key');
        if($dept_id == Department::DEFAULT_DEPT_ID) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '禁止修改默认分组');
            return ;
        }

        $deptManager = $this->getDeptManager();
        $department = $deptManager->getDepartment($dept_id);
        if (null == $department) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '无效的分组ID: ' . $dept_id);
            return ;
        }

        $form = new DepartmentForm($deptManager, $department);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $department->setDeptName($data['name']);
                $deptManager->saveModifiedEntity($department);

                return $this->getMessagePlugin()->show(
                    '部门已更新',
                    '部门: ' . $data['name'] . ' 的信息已经更新!',
                    $this->url()->fromRoute('admin/dept'),
                    '返回',
                    3
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
     * View a department all members
     *
     * @return ViewModel
     */
    public function membersAction()
    {
        $dept_id = $this->params()->fromRoute('key');
        $dept = $this->getDeptManager()->getDepartment($dept_id);
        if (null == $dept) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '无效的部门ID: ' . $dept_id);
            return ;
        }

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
     * Ajax save department members
     *
     * @return JsonModel
     */
    public function updateMembersAction()
    {

        $json = ['success' => false];

        $dept_id = $this->params()->fromRoute('key');
        if ($dept_id == Department::DEFAULT_DEPT_ID) {
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '禁止更新基础分组信息');
            return new JsonModel($json);
        }

        if($this->getRequest()->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $deptManager = $this->getDeptManager();
            $dept = $deptManager->getDepartment($dept_id);
            if (null == $dept) {
                $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '无效的分组编号:' . $dept_id);
                return new JsonModel($json);
            }

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
     * Controller and actions registry
     *
     * @return array
     */
    public static function ComponentRegistry()
    {
        $item = self::CreateControllerRegistry(__CLASS__, '部门管理', 'admin/dept', 1, 'users', 12);

        $item['actions']['index'] = self::CreateActionRegistry('index', '查看部门列表', 1, 'bars', 9);
        $item['actions']['add'] = self::CreateActionRegistry('add', '创建新部门', 1, 'plus', 1);

        $item['actions']['status'] = self::CreateActionRegistry('status', '启用/禁用部门');
        $item['actions']['edit'] = self::CreateActionRegistry('edit', '修改部门信息');
        $item['actions']['members'] = self::CreateActionRegistry('members', '查看部门成员');
        $item['actions']['update-members'] = self::CreateActionRegistry('update-members', '分配部门成员');

        return $item;
    }

}