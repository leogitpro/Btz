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
use Admin\Entity\Action;
use Admin\Entity\Department;
use Admin\Entity\Member;
use Admin\Service\AclManager;
use Admin\Service\ComponentManager;
use Admin\Service\DepartmentManager;
use Admin\Service\MemberManager;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class AclController extends BaseController
{

    /**
     * @var AclManager
     */
    private $aclManager;

    /**
     * @var MemberManager
     */
    private $memberManager;

    /**
     * @var DepartmentManager
     */
    private $deptManager;

    /**
     * @var ComponentManager
     */
    private $componentManager;



    public function autoRegisterComponent()
    {
        return [
            'controller' => __CLASS__,
            'name' => 'Access control',
            'route' => 'admin/acl',
            'menu' => true,
            'rank' => 0,
            'icon' => 'cogs',
            'actions' => [
                [
                    'action' => 'member',
                    'name' => 'Members ACL',
                    'menu' => true,
                    'rank' => 0,
                    'icon' => 'users',
                ],
                [
                    'action' => 'acl-member',
                    'name' => 'List member ACL',
                ],
                [
                    'action' => 'acl-member-dispatch',
                    'name' => 'Modify member ACL',
                ],
                [
                    'action' => 'department',
                    'name' => 'Departments ACL',
                    'menu' => true,
                    'rank' => 0,
                    'icon' => 'bars',
                ],
                [
                    'action' => 'acl-department',
                    'name' => 'Department access control',
                ],
            ],
        ];
    }


    public function onDispatch(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();

        $this->aclManager = $sm->get(AclManager::class);
        $this->memberManager = $sm->get(MemberManager::class);
        $this->deptManager = $sm->get(DepartmentManager::class);
        $this->componentManager = $sm->get(ComponentManager::class);

        return parent::onDispatch($e);
    }


    /**
     * List grant members
     *
     * @return ViewModel
     */
    public function memberAction()
    {
        // Page information
        $page = (int)$this->params()->fromRoute('key', 1);
        $size = 10;

        // Get pagination helper
        $viewHelperManager = $this->getEvent()->getApplication()->getServiceManager()->get("ViewHelperManager");
        $paginationHelper = $viewHelperManager->get('pagination');

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/acl', ['action' => 'member', 'key' => '%d']));
        $paginationHelper->setCount($this->memberManager->getMembersCount());

        // Render view data
        $members = $this->memberManager->getMembersByLimitPage($page, $size);

        return new ViewModel([
            'entities' => $members,
            'activeId' => __METHOD__,
        ]);

    }


    /**
     * Member acl resources list
     *
     * @return void|ViewModel
     */
    public function aclMemberAction()
    {
        $key = $this->params()->fromRoute('key', 0);
        $params = explode('-', $key);
        $member_id = (int)array_shift($params);

        if ($member_id == Member::DEFAULT_MEMBER_ID) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Forbid control default administrator acl');
            return ;
        }

        $member = $this->memberManager->getMember($member_id);
        if (null == $member || Member::STATUS_ACTIVATED != $member->getMemberStatus()) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Invalid administrator id:' . $member_id);
            return ;
        }

        // Page information
        $page = (int)array_shift($params);
        if ($page < 1) { $page = 1; }
        $size = 4;

        // Get pagination helper
        $viewHelperManager = $this->getEvent()->getApplication()->getServiceManager()->get("ViewHelperManager");
        $paginationHelper = $viewHelperManager->get('pagination');

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/acl', ['action' => 'acl-member', 'key' =>  $member_id . '-%d']));
        $paginationHelper->setCount($this->componentManager->getComponentsCount());

        $data = $this->componentManager->getComponentsWithActionsByLimitPage($page, $size);

        $rows = $this->aclManager->getMemberAllAcls($member_id);
        $acls = [];
        foreach ($rows as $row) {
            if ($row instanceof AclMember) {
                $acls[$row->getActionId()] = $row;
            }
        }

        return new ViewModel([
            'member' => $member,
            'components' => $data['components'],
            'actions' => $data['actions'],
            'acls' => $acls,
            'activeId' => __CLASS__,
        ]);
    }


    /**
     * Save modified member acl
     *
     * @return void|JsonModel
     */
    public function aclMemberDispatchAction()
    {
        $result = [
            'success' => false,
            'code' => 0,
            'message' => '',
        ];

        $key = $this->params()->fromRoute('key', 0);
        $params = explode('-', $key);
        $member_id = (int)array_shift($params);

        if ($member_id == Member::DEFAULT_MEMBER_ID) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Forbid control default administrator acl');
            return ;
        }

        $member = $this->memberManager->getMember($member_id);
        if (null == $member || Member::STATUS_ACTIVATED != $member->getMemberStatus()) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Invalid administrator id:' . $member_id);
            return ;
        }

        $action_id = (int)array_shift($params);
        $action = $this->componentManager->getAction($action_id);
        if (null == $action || Action::STATUS_VALIDITY != $action->getActionStatus()) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Invalid action id:' . $action_id);
            return ;
        }

        $status = (int)array_shift($params);
        $list = AclMember::getAclStatusList();
        if (!array_key_exists($status, $list)) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Invalid status:' . $status);
            return ;
        }

        $this->aclManager->saveMemberAcl($member_id, $action_id, $status);

        $result['success'] = true;
        return new JsonModel($result);
    }


    /**
     * List grant departments
     *
     * @return ViewModel
     */
    public function departmentAction()
    {
        // Page information
        $page = (int)$this->params()->fromRoute('key', 1);
        $size = 10;

        // Get pagination helper
        $viewHelperManager = $this->getEvent()->getApplication()->getServiceManager()->get("ViewHelperManager");
        $paginationHelper = $viewHelperManager->get('pagination');

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/acl', ['action' => 'department', 'key' => '%d']));
        $paginationHelper->setCount($this->deptManager->getDepartmentsCount());

        // Render view data
        $entities = $this->deptManager->getDepartmentsByLimitPage($page, $size);

        return new ViewModel([
            'entities' => $entities,
            'activeId' => __METHOD__,
        ]);

    }


    /**
     * Acl department resource list
     *
     * @return void|ViewModel
     */
    public function aclDepartmentAction()
    {
        $key = $this->params()->fromRoute('key', 0);
        $params = explode('-', $key);
        $dept_id = (int)array_shift($params);

        if ($dept_id == Department::DEFAULT_DEPT_ID) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Forbid control default department acl');
            return ;
        }

        $dept = $this->deptManager->getDepartment($dept_id);
        if (null == $dept || Department::STATUS_VALID != $dept->getDeptStatus()) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Invalid department id:' . $dept_id);
            return ;
        }

        // Page information
        $page = (int)array_shift($params);
        if ($page < 1) { $page = 1; }
        $size = 4;

        // Get pagination helper
        $viewHelperManager = $this->getEvent()->getApplication()->getServiceManager()->get("ViewHelperManager");
        $paginationHelper = $viewHelperManager->get('pagination');

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/acl', ['action' => 'acl-member', 'key' =>  $dept_id . '-%d']));
        $paginationHelper->setCount($this->componentManager->getComponentsCount());

        $data = $this->componentManager->getComponentsWithActionsByLimitPage($page, $size);

        $rows = $this->aclManager->getDepartmentAllAcls($dept_id);
        $acls = [];
        foreach ($rows as $row) {
            if ($row instanceof AclDepartment) {
                $acls[$row->getActionId()] = $row;
            }
        }

        return new ViewModel([
            'dept' => $dept,
            'components' => $data['components'],
            'actions' => $data['actions'],
            'acls' => $acls,
            'activeId' => __CLASS__,
        ]);
    }



    /**
     * Save modified department acl
     *
     * @return void|JsonModel
     */
    public function aclDepartmentDispatchAction()
    {
        $result = [
            'success' => false,
            'code' => 0,
            'message' => '',
        ];

        $key = $this->params()->fromRoute('key', 0);
        $params = explode('-', $key);
        $dept_id = (int)array_shift($params);

        if ($dept_id == Department::DEFAULT_DEPT_ID) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Forbid control default department acl');
            return ;
        }

        $dept = $this->deptManager->getDepartment($dept_id);
        if (null == $dept || Department::STATUS_VALID != $dept->getDeptStatus()) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Invalid department id:' . $dept_id);
            return ;
        }

        $action_id = (int)array_shift($params);
        $action = $this->componentManager->getAction($action_id);
        if (null == $action || Action::STATUS_VALIDITY != $action->getActionStatus()) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Invalid action id:' . $action_id);
            return ;
        }

        $status = (int)array_shift($params);
        $list = AclDepartment::getAclStatusList();
        if (!array_key_exists($status, $list)) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Invalid status:' . $status);
            return ;
        }

        $this->aclManager->saveDepartmentAcl($dept_id, $action_id, $status);

        $result['success'] = true;
        return new JsonModel($result);
    }


}