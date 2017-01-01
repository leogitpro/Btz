<?php
/**
 * DepartmentController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Controller;


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

                $this->deptManager->craeteDepartment($data['name']);

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