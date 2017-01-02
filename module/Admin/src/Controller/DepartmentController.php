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
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class DepartmentController extends AbstractActionController
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


    public function indexAction()
    {
        $rows = $this->deptManager->getAllDepartments();

        return new ViewModel(['rows' => $rows]);
    }


    /**
     * Setting department status api
     */
    public function statusAction()
    {
        $dept_id = (int)$this->params()->fromRoute('key', 0);
        if($dept_id <= 1) {
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
            $department->setDeptStatus(Department::STATUS_INVALID);
        } else {
            $department->setDeptStatus(Department::STATUS_VALID);
        }

        $this->deptManager->saveModifiedDepartment($department);

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
        if($dept_id <= 1) {
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