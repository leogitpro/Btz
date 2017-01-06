<?php
/**
 * Department and member relationship controller
 *
 * User: leo
 */

namespace Admin\Controller;


use Admin\Entity\Department;
use Admin\Entity\DepartmentMember;
use Admin\Entity\Member;
use Admin\Service\DepartmentManager;
use Admin\Service\DepartmentMemberRelationManager;
use Admin\Service\MemberManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class DepartmentMemberRelationController extends AbstractActionController
{

    /**
     * @var DepartmentMemberRelationManager
     */
    private $relationshipManager;

    /**
     * @var MemberManager
     */
    private $memberManager;

    /**
     * @var DepartmentManager
     */
    private $departmentManager;



    public function onDispatch(MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();

        $this->relationshipManager = $serviceManager->get(DepartmentMemberRelationManager::class);
        $this->memberManager = $serviceManager->get(MemberManager::class);
        $this->departmentManager = $serviceManager->get(DepartmentManager::class);

        return parent::onDispatch($e);
    }


    public function indexAction()
    {
        return $this->getResponse();
    }

    /**
     * View a department with all members relationship
     */
    public function departmentMembersAction()
    {
        $dept_id = (int)$this->params()->fromRoute('key', 0);

        $dept = $this->departmentManager->getDepartment($dept_id);
        if (null == $dept) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Invalid department id:' . $dept_id);
            return ;
        }

        $data = [
            'inner' => [],
            'outer' => [],
            'dept' => $dept,
        ];

        $relations = $this->relationshipManager->getDepartmentMemberRelations($dept_id);
        $joined = [];
        foreach ($relations as $relation) {
            if ($relation instanceof DepartmentMember) {
                $joined[$relation->getMemberId()] = $relation;
            }
        }

        $members = $this->memberManager->getMembers();
        foreach ($members as $member) {
            if ($member instanceof Member) {

                if (Member::DEFAULT_MEMBER_ID == $member->getMemberId()) {
                    continue;
                }

                if (isset($joined[$member->getMemberId()])) {
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
     * AJAX save department to members relationship
     *
     * @return JsonModel
     */
    public function saveDepartmentMembersAction()
    {
        $json = ['success' => false];

        $dept_id = (int)$this->params()->fromRoute('key', 0);
        if ($dept_id == Department::DEFAULT_DEPT_ID) {
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Forbid edit default department members');
            return new JsonModel($json);
        }

        if($this->getRequest()->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $dept = $this->departmentManager->getDepartment($dept_id);
            if (null == $dept) {
                $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Invalid department id:' . $dept_id);
                return new JsonModel($json);
            }

            $selected = (array)$this->params()->fromPost('selected');
            $joined = [];
            foreach($selected as $id) {
                $id = (int)$id;
                if ($id == Member::DEFAULT_MEMBER_ID) {
                    continue;
                }
                $joined[$id] = $id;
            }

            $this->relationshipManager->closedOneDepartment($dept_id);
            $this->relationshipManager->openedOneDepartment($dept_id);

            foreach ($joined as $member_id) {
                $this->relationshipManager->increaseMemberToDepartment($member_id, $dept_id);
            }

            $json['success'] = true;
        } else {

        }

        return new JsonModel($json);
    }



    /**
     * View a member with all departments relationship
     */
    public function memberDepartmentsAction()
    {
        $member_id = (int)$this->params()->fromRoute('key', 0);
        $member = $this->memberManager->getMember($member_id);
        if (null == $member) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Invalid member id:' . $member_id);
            return ;
        }

        $data = [
            'inner' => [],
            'outer' => [],
            'member' => $member, //['member_id' => $member->getMemberId(), 'member_name' => $member->getMemberName(),],
        ];

        $relations = $this->relationshipManager->getMemberDepartmentRelations($member_id);
        $joined = [];
        foreach ($relations as $relation) {
            if ($relation instanceof DepartmentMember) {
                $joined[$relation->getDeptId()] = $relation;
            }
        }

        $departments = $this->departmentManager->getDepartments();
        foreach ($departments as $department) {
            if ($department instanceof Department) {

                if (Department::DEFAULT_DEPT_ID == $department->getDeptId()) {
                    continue;
                }

                if (isset($joined[$department->getDeptId()])) {
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
     * AJAX save member to departments relationship
     *
     * @return JsonModel
     */
    public function saveMemberDepartmentsAction()
    {
        $json = ['success' => false];

        $member_id = (int)$this->params()->fromRoute('key', 0);
        if ($member_id == Member::DEFAULT_MEMBER_ID) {
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Forbid edit root member departments');
            return new JsonModel($json);
        }

        if($this->getRequest()->isPost() && $this->getRequest()->isXmlHttpRequest()) {

            $member = $this->memberManager->getMember($member_id);
            if (null == $member) {
                $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Invalid member id:' . $member_id);
                return new JsonModel($json);
            }

            $selected = (array)$this->params()->fromPost('selected');
            $joined = [];
            foreach($selected as $id) {
                $id = (int)$id;
                if ($id == Department::DEFAULT_DEPT_ID) {
                    continue;
                }
                $joined[$id] = $id;
            }

            $this->relationshipManager->closedOneMember($member_id); // Clean all old relationship
            $this->relationshipManager->openedOneMember($member_id); // For restore default department

            foreach ($joined as $dept_id) {
                $this->relationshipManager->increaseMemberToDepartment($member_id, $dept_id);
            }

            $json['success'] = true;
        } else {

        }

        return new JsonModel($json);
    }

}