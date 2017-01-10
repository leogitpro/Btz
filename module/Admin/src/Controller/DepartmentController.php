<?php
/**
 * DepartmentController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Controller;


use Admin\Entity\Department;
use Admin\Form\DepartmentForm;
use Admin\Service\DepartmentManager;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class DepartmentController extends BaseController
{

    /**
     * @var DepartmentManager
     */
    private $deptManager;

    public function onDispatch(MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();

        $this->deptManager = $serviceManager->get(DepartmentManager::class);


        return parent::onDispatch($e);
    }


    public function autoRegisterComponent()
    {
        return [
            'controller' => __CLASS__,
            'name' => 'Department',
            'route' => 'admin/dept',
            'menu' => true,
            'icon' => 'users',
            'rank' => 0,
            'actions' => [
                [
                    'action' => 'index',
                    'name' => 'Departments',
                    'menu' => true,
                    'icon' => 'bars',
                ],
                [
                    'action' => 'add',
                    'name' => 'New department',
                    'menu' => true,
                    'icon' => 'plus',
                ],
                [
                    'action' => 'edit',
                    'name' => 'Edit department',
                ],
                [
                    'action' => 'status',
                    'name' => 'Change status',
                ],
            ],
        ];

    }


    /**
     * List departments
     *
     * @return ViewModel
     */
    public function indexAction()
    {

        $viewHelperManager = $this->getEvent()->getApplication()->getServiceManager()->get('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        $page = (int)$this->params()->fromRoute('key', 1);
        if ($page < 1) {
            $page = 1;
        }

        $size = 10;
        $count = $this->deptManager->getAllDepartmentsCount();

        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/dept', ['action' => 'index', 'key' => '%d']));

        $rows = $this->deptManager->getAllDepartmentByLimitPage($page, $size);

        return new ViewModel(['rows' => $rows]);
    }


    /**
     * Setting department status api
     */
    public function statusAction()
    {
        $dept_id = (int)$this->params()->fromRoute('key', 0);
        if($dept_id == Department::DEFAULT_DEPT_ID) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Forbid update Default department status');
            return ;
        }

        $department = $this->deptManager->getDepartment($dept_id);
        if (null == $department) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Invalid department id: ' . $dept_id);
            return ;
        }

        if (Department::STATUS_VALID == $department->getDeptStatus()) {
            $status = Department::STATUS_INVALID;
        } else {
            $status = Department::STATUS_VALID;
        }

        $this->deptManager->updateDepartmentStatus($department, $status);

        return $this->getMessagePlugin()->show(
            'Department updated',
            'The department: ' . $department->getDeptName() . ' status has been updated success!',
            $this->url()->fromRoute('admin/dept'),
            'Departments',
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
        $dept_id = (int)$this->params()->fromRoute('key', 0);
        if($dept_id == Department::DEFAULT_DEPT_ID) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Forbid edit Default department');
            return ;
        }

        $department = $this->deptManager->getDepartment($dept_id);
        if (null == $department) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . ' Invalid department id: ' . $dept_id);
            return ;
        }

        $form = new DepartmentForm($this->deptManager, $department);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $department->setDeptName($data['name']);
                $this->deptManager->saveModifiedDepartment($department);

                return $this->getMessagePlugin()->show(
                    'Department updated',
                    'The department: ' . $data['name'] . ' has been updated success!',
                    $this->url()->fromRoute('admin/dept'),
                    'Departments',
                    3
                );
            }
        }

        return new ViewModel([
            'form' => $form,
            'dept' => $department,
        ]);

    }


    /**
     * Create department page
     *
     * @return ViewModel
     */
    public function addAction()
    {

        $form = new DepartmentForm($this->deptManager);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $this->deptManager->createDepartment($data['name']);

                return $this->getMessagePlugin()->show(
                    'Department created',
                    'The new department: ' . $data['name'] . ' has been created success!',
                    $this->url()->fromRoute('admin/dept'),
                    'Departments',
                    3
                );
            }
        }

        return new ViewModel([
            'form' => $form,
        ]);

    }

}