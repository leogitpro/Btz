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
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class ComponentController extends AdminBaseController
{

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
        $viewHelperManager = $this->getSm("ViewHelperManager");
        $paginationHelper = $viewHelperManager->get('pagination');

        $componentManager = $this->getComponentManager();

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/component', ['action' => 'index', 'key' => '%d']));
        $paginationHelper->setCount($componentManager->getComponentsCount());

        // Render view data
        $rows = $componentManager->getComponentsByLimitPage($page, $size);

        /**
        foreach ($rows as $row) {
            if ($row instanceof Component) {
                $actions = $row->getActions();
                foreach ($actions as $action) {
                    if ($action instanceof Action) {
                        $this->getLoggerPlugin()->debug('action: ' . $action->getActionName());
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
     * Ajax call sync component data
     */
    public function syncAction()
    {
        ignore_user_abort(true);
        set_time_limit(0);

        $result = ['success' => false, 'code' => 0, 'message' => ''];

        // if (!$this->getRequest()->isXmlHttpRequest()) { return $this->getResponse(); }

        $controllers = $this->getConfigPlugin()->get('controllers.factories');
        $items = [];
        foreach($controllers as $controllerClassName => $factory) {
            if (0 !== strpos($controllerClassName, __NAMESPACE__)) {
                continue;
            }

            if (method_exists($controllerClassName, 'ComponentRegistry')) {
                $items[] = $controllerClassName::ComponentRegistry();
            }
        }

        //echo '<p>Origin</p><pre>'; print_r($items); echo '</pre><hr>';

        //$items = [];
        $this->getComponentManager()->syncComponents($items);

        $result['success'] = true;
        return new JsonModel($result);
    }


    /**
     * Remove component
     */
    public function deleteAction()
    {
        $componentManager = $this->getComponentManager();

        $component_class = base64_decode($this->params()->fromRoute('key'));

        $component = $componentManager->getComponent($component_class);
        if (!($component instanceof Component)) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '无效的模块识别:' . $component_class);
            return ;
        }

        // Clean the acl
        $actions = $component->getActions();
        foreach ($actions as $action) {
            if ($action instanceof Action) {
                $this->getAclManager()->removeAction($action->getActionId());
            }
        }

        $comName = $component->getComName();

        // Delete the component and actions
        $componentManager->removeEntity($component);

        return $this->getMessagePlugin()->show(
            '模块已删除',
            'The Component: ' . $comName . ' 及所有的功能接口已全部删除!',
            $this->url()->fromRoute('admin/component'),
            '返回',
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
        $component_class = base64_decode($this->params()->fromRoute('key'));

        $component = $this->getComponentManager()->getComponent($component_class);
        if (!($component instanceof Component)) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '无效的模块识别:' . $component_class);
            return ;
        }


        $viewModel = new ViewModel();
        $viewModel->setVariables([
            'component' => $component,
        ]);
        $viewModel->setTerminal(true);
        return $viewModel;
    }


    /**
     * Ajax remove a component's one action
     */
    public function removeAction()
    {
        $componentManager = $this->getComponentManager();
        $action_id = $this->params()->fromRoute('key');
        $action = $componentManager->getAction($action_id);
        if (!($action instanceof Action)) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '无效的接口编号:' . $action_id);
            return ;
        }

        // Clean the acl
        $this->getAclManager()->removeAction($action_id);

        // Delete the action
        $componentManager->removeEntity($action);

        return new JsonModel(['success' => true]);
    }



    /**
     * Controller and actions registry
     *
     * @return array
     */
    public static function ComponentRegistry()
    {
        $item = self::CreateControllerRegistry(__CLASS__, '系统模块', 'admin/component', 1, 'cubes', 14);

        $item['actions']['index'] = self::CreateActionRegistry('index', '查看模块列表', 1, 'bars', 0);

        $item['actions']['sync'] = self::CreateActionRegistry('sync', '同步系统模块');
        $item['actions']['delete'] = self::CreateActionRegistry('delete', '删除某个模块');
        $item['actions']['actions'] = self::CreateActionRegistry('actions', '查看模块功能列表');
        $item['actions']['remove'] = self::CreateActionRegistry('remove', '删除某个功能接口');

        return $item;
    }


}