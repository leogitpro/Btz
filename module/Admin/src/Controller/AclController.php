<?php
/**
* AclController.php
*
* @author: Leo <camworkster@gmail.com>
* @version: 1.0
*/


namespace Admin\Controller;

use Admin\Entity\AclDepartment;
use Admin\Entity\AclMember;
use Admin\Entity\Department;
use Admin\Entity\Member;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class AclController extends AdminBaseController
{

    /**
     * List grant members
     *
     * @return ViewModel
     */
    public function membersAction()
    {
        // Page information
        $page = (int)$this->params()->fromRoute('key', 1);
        $size = 10;

        // Get pagination helper
        $viewHelperManager = $this->getSm("ViewHelperManager");
        $paginationHelper = $viewHelperManager->get('pagination');

        $memberManager = $this->getMemberManager();

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/acl', ['action' => 'members', 'key' => '%d']));
        $paginationHelper->setCount($memberManager->getMembersCount());

        // Render view data
        $members = $memberManager->getMembersByLimitPage($page, $size);

        return new ViewModel([
            'entities' => $members,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * Member acl resources list
     *
     * @return ViewModel
     */
    public function memberAction()
    {
        $key = $this->params()->fromRoute('key', 0);
        $params = explode('_', $key);
        $member_id = (string)array_shift($params);

        if ($member_id == Member::DEFAULT_MEMBER_ID) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '禁止查看超级管理员进行权限配置');
            return ;
        }

        $member = $this->getMemberManager()->getMember($member_id);
        if (null == $member || Member::STATUS_ACTIVATED != $member->getMemberStatus()) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '无效的成员编号:' . $member_id);
            return ;
        }

        // Page information
        $page = (int)array_shift($params);
        if ($page < 1) { $page = 1; }
        $size = 4;

        // Get pagination helper
        $viewHelperManager = $this->getSm("ViewHelperManager");
        $paginationHelper = $viewHelperManager->get('pagination');

        $componentManager = $this->getComponentManager();

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/acl', ['action' => 'member', 'key' =>  $member_id . '_%d']));
        $paginationHelper->setCount($componentManager->getComponentsCount());

        $components = $componentManager->getComponentsByLimitPage($page, $size);

        $rows = $this->getAclManager()->getMemberAndActionAllAclByMember($member_id);
        $acl = [];
        foreach ($rows as $row) {
            if ($row instanceof AclMember) {
                $acl[$row->getAction()] = $row;
            }
        }

        return new ViewModel([
            'member' => $member,
            'components' => $components,
            'acl' => $acl,
            'activeId' => __CLASS__,
        ]);
    }


    /**
     * Save modified member acl
     *
     * @return JsonModel
     */
    public function memberDispatchAction()
    {
        $result = ['success' => false, 'code' => 0, 'message' => ''];

        $key = $this->params()->fromRoute('key', 0);
        $params = explode('_', $key);
        $member_id = (string)array_shift($params);

        if ($member_id == Member::DEFAULT_MEMBER_ID) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '禁止对超级管理员进行权限配置');
            return ;
        }

        $member = $this->getMemberManager()->getMember($member_id);
        if (null == $member || Member::STATUS_ACTIVATED != $member->getMemberStatus()) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '无效的成员编号: ' . $member_id);
            return ;
        }

        $action_id = (string)array_shift($params);
        $action = $this->getComponentManager()->getAction($action_id);
        if (null == $action) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '无效的功能编号: ' . $action_id);
            return ;
        }

        $status = (int)array_shift($params);
        $list = AclMember::getAclStatusList();
        if (!array_key_exists($status, $list)) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '非法的授权类型: ' . $status);
            return ;
        }

        $this->getAclManager()->setMemberAndActionAcl($member_id, $action_id, $status);

        $result['success'] = true;
        return new JsonModel($result);
    }



    /**
     * List grant departments
     *
     * @return ViewModel
     */
    public function departmentsAction()
    {
        // Page information
        $page = (int)$this->params()->fromRoute('key', 1);
        $size = 10;

        // Get pagination helper
        $viewHelperManager = $this->getSm("ViewHelperManager");
        $paginationHelper = $viewHelperManager->get('pagination');

        $deptManager = $this->getDeptManager();

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/acl', ['action' => 'departments', 'key' => '%d']));
        $paginationHelper->setCount($deptManager->getDepartmentsCount());

        // Render view data
        $entities = $deptManager->getDepartmentsByLimitPage($page, $size);

        return new ViewModel([
            'entities' => $entities,
            'activeId' => __METHOD__,
        ]);

    }


    /**
     * Acl department resource list
     *
     * @return ViewModel
     */
    public function departmentAction()
    {
        $key = $this->params()->fromRoute('key', 0);
        $params = explode('_', $key);
        $dept_id = array_shift($params);

        if ($dept_id == Department::DEFAULT_DEPT_ID) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '禁止操作基础部门');
            return ;
        }


        $dept = $this->getDeptManager()->getDepartment($dept_id);
        if (null == $dept || Department::STATUS_VALID != $dept->getDeptStatus()) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '无效的部门ID:' . $dept_id);
            return ;
        }

        // Page information
        $page = (int)array_shift($params);
        if ($page < 1) { $page = 1; }
        $size = 4;

        // Get pagination helper
        $viewHelperManager = $this->getSm("ViewHelperManager");
        $paginationHelper = $viewHelperManager->get('pagination');

        $componentManager = $this->getComponentManager();

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/acl', ['action' => 'department', 'key' =>  $dept_id . '_%d']));
        $paginationHelper->setCount($componentManager->getComponentsCount());

        $components = $componentManager->getComponentsByLimitPage($page, $size);

        $rows = $this->getAclManager()->getDepartmentAndActionAllAclByDepartment($dept_id);
        $acl = [];
        foreach ($rows as $row) {
            if ($row instanceof AclDepartment) {
                $acl[$row->getAction()] = $row;
            }
        }

        return new ViewModel([
            'dept' => $dept,
            'components' => $components,
            'acl' => $acl,
            'activeId' => __CLASS__,
        ]);
    }


    /**
     * Save modified department acl
     */
    public function departmentDispatchAction()
    {
        $result = ['success' => false, 'code' => 0, 'message' => ''];

        $key = $this->params()->fromRoute('key', 0);
        $params = explode('_', $key);
        $dept_id = (string)array_shift($params);

        if ($dept_id == Department::DEFAULT_DEPT_ID) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '禁止配置基础部门权限');
            return ;
        }

        $dept = $this->getDeptManager()->getDepartment($dept_id);
        if (null == $dept || Department::STATUS_VALID != $dept->getDeptStatus()) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '无效的部门ID:' . $dept_id);
            return ;
        }

        $action_id = (string)array_shift($params);
        $action = $this->getComponentManager()->getAction($action_id);
        if (null == $action) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '无效的功能:' . $action_id);
            return ;
        }

        $status = (int)array_shift($params);
        $list = AclDepartment::getAclStatusList();
        if (!array_key_exists($status, $list)) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '非法的权限配置信息:' . $status);
            return ;
        }

        $this->getAclManager()->setDepartmentAndActionAcl($dept_id, $action_id, $status);

        $result['success'] = true;
        return new JsonModel($result);
    }


    /**
     * Controller and actions registry
     *
     * @return array
     */
    public static function ComponentRegistry()
    {
        $item = self::CreateControllerRegistry(__CLASS__, '权限控制', 'admin/acl', 1, 'cogs', 16);

        $item['actions']['members'] = self::CreateActionRegistry('members', '个人权限配置', 1, 'user', 9);
        $item['actions']['departments'] = self::CreateActionRegistry('departments', '集体权限配置', 1, 'users', 19);

        $item['actions']['member'] = self::CreateActionRegistry('member', '查看个人权限', 0, null, 8);
        $item['actions']['member-dispatch'] = self::CreateActionRegistry('member-dispatch', '配置个人权限', 0, null, 7);

        $item['actions']['department'] = self::CreateActionRegistry('department', '查看集体权限', 0, null, 18);
        $item['actions']['department-dispatch'] = self::CreateActionRegistry('department-dispatch', '配置集体权限', 0, null, 17);

        return $item;
    }

}