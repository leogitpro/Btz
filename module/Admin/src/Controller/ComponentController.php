<?php
/**
 * ComponentController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Controller;


use Admin\Entity\Action;
use Admin\Entity\Component;
use Admin\Service\ComponentManager;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ComponentController extends BaseController
{
    /**
     * @var ComponentManager
     */
    private $componentManager;



    public function onDispatch(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();

        $this->componentManager = $sm->get(ComponentManager::class);

        return parent::onDispatch($e);
    }



    public function autoRegisterComponent()
    {
        return [
            'controller' => __CLASS__,
            'name' => '系统模块',
            'route' => 'admin/component',
            'menu' => true,
            'rank' => 14,
            'icon' => 'cubes',
            'actions' => [
                [
                    'action' => 'index',
                    'name' => '查看模块列表',
                    'menu' => true,
                    'rank' => 0,
                    'icon' => 'bars',
                ],
                [
                    'action' => 'status',
                    'name' => '启用/禁用模块',
                ],
                [
                    'action' => 'actions',
                    'name' => '查看模块功能列表',
                ],
                [
                    'action' => 'actionstatus',
                    'name' => '启用/禁用模块功能',
                ],
                [
                    'action' => 'sync',
                    'name' => '同步系统模块',
                ],
            ],
        ];

    }


    /**
     * Showing components list
     *
     * @return ViewModel
     */
    public function indexAction()
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
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/component', ['action' => 'index', 'key' => '%d']));
        $paginationHelper->setCount($this->componentManager->getAllComponentsCount());

        // Render view data
        $components = $this->componentManager->getAllComponentsByLimitPage($page, $size);

        return new ViewModel([
            'rows' => $components,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * Change component status
     */
    public function statusAction()
    {
        $component_id = $this->params()->fromRoute('key', 0);
        $component = $this->componentManager->getComponent($component_id);
        if (!($component instanceof Component)) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Invalid component id:' . $component_id);
            return ;
        }

        if ($component->getComStatus() == Component::STATUS_INVALID) {
            $component->setComStatus(Component::STATUS_VALIDITY);
        } else {
            $component->setComStatus(Component::STATUS_INVALID);
        }

        $component = $this->componentManager->saveModifiedComponent($component);

        return $this->getMessagePlugin()->show(
            'Component updated',
            'The Component: ' . $component->getComName() . ' status has been updated!',
            $this->url()->fromRoute('admin/component'),
            'Components',
            3
        );
    }


    /**
     * Component actions list
     *
     * @return ViewModel
     */
    public function actionsAction()
    {
        $component_id = $this->params()->fromRoute('key', 0);

        $component = $this->componentManager->getComponent($component_id);
        if (!($component instanceof Component)) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Invalid component id:' . $component_id);
            return ;
        }

        $actions = $this->componentManager->getComponentAllActions($component);

        $viewModel = new ViewModel();
        $viewModel->setVariables([
            'entity' => $component,
            'entities' => $actions,
        ]);
        $viewModel->setTerminal(true);
        return $viewModel;
    }


    /**
     * Ajax call change action status
     *
     * @return JsonModel
     */
    public function actionstatusAction()
    {
        $action_id = $this->params()->fromRoute('key', 0);
        $action = $this->componentManager->getAction($action_id);
        if (!($action instanceof Action)) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . 'Invalid action id:' . $action_id);
            return ;
        }

        if ($action->getActionStatus() == Action::STATUS_VALIDITY) {
            $action->setActionStatus(Action::STATUS_INVALID);
        } else {
            $action->setActionStatus(Action::STATUS_VALIDITY);
        }

        $this->componentManager->saveModifiedAction($action);

        return new JsonModel(['success' => true]);
    }


    /**
     * Ajax call sync component data
     */
    public function syncAction()
    {
        ignore_user_abort(true);
        set_time_limit(0);

        $result = [
            'success' => false,
            'code' => 0,
            'message' => '',
        ];

        // if (!$this->getRequest()->isXmlHttpRequest()) { return $this->getResponse(); }

        $this->getLoggerPlugin()->debug(__METHOD__ . PHP_EOL . 'Start sync component and actions');

        $controllers = $this->getConfigPlugin()->get('controllers.factories');
        $controllerManager = $this->getEvent()->getApplication()->getServiceManager()->get('ControllerManager');

        $items = [];
        foreach($controllers as $controllerClassName => $factory) {
            if (0 !== strpos($controllerClassName, __NAMESPACE__)) {
                continue;
            }

            if (!$controllerManager->has($controllerClassName)) {
                continue;
            }

            try {

                $controllerInstance = $controllerManager->get($controllerClassName);
                $method = 'autoRegisterComponent';
                if (method_exists($controllerInstance, $method)) {
                    $items[] = $controllerInstance->$method();
                }

            } catch (\Exception $e) {
                $this->getLoggerPlugin(__METHOD__ . PHP_EOL . $e->getMessage());
            }
        }

        //$items = [$this->autoRegisterComponent()];
        //echo '<p>Origin</p><pre>'; print_r($items); echo '</pre><hr>';

        $this->componentManager->syncComponents($items);

        $this->getLoggerPlugin()->debug(__METHOD__ . PHP_EOL . 'End synced component and actions');

        $result['success'] = true;
        return new JsonModel($result);
    }

}