<?php
/**
 * Department and member relationship controller
 *
 * User: leo
 */

namespace Admin\Controller;


use Admin\Entity\Department;
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
            'dept' => [
                'dept_id' => $dept->getDeptId(),
                'dept_name' => $dept->getDeptName(),
                'dept_members' => $dept->getDeptMembers(),
            ],
        ];

        $ids = $this->relationshipManager->getDepartmentMemberIds($dept_id);

        $rows = $this->memberManager->getMembers();
        foreach ($rows as $row) {
            if ($row instanceof Member) {
                $item = [
                    'member_id' => $row->getMemberId(),
                    'member_name' => $row->getMemberName(),
                ];

                if (isset($ids[$row->getMemberId()])) {
                    array_push($data['inner'], $item);
                } else {
                    array_push($data['outer'], $item);
                }
            }
        }

        return new JsonModel($data);
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

        $ids = $this->relationshipManager->getMemberDepartmentIds($member_id);

        $rows = $this->departmentManager->getDepartments();
        foreach ($rows as $row) {
            if ($row instanceof Department) {
                if (isset($ids[$row->getDeptId()])) {
                    array_push($data['inner'], $row);
                } else {
                    array_push($data['outer'], $row);
                }
            }
        }

        $viewModel = new ViewModel();
        $viewModel->setVariables(['data' => $data]);
        $viewModel->setTerminal(true);
        return $viewModel;

    }


    public function saveMemberDepartmentsAction()
    {
        $selected = $this->params()->fromPost('selected');
        if (is_array($selected)) {
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'inner ids:' . implode('-', $selected));
        }

        $json = ['success' => true];
        return new JsonModel($json);
    }

}