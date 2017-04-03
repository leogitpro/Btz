<?php
/**
 * ComponentController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Controller;


use Admin\Entity\Action;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


/**
 * 系统组件管理
 *
 * Class ComponentController
 * @package Admin\Controller
 */
class ComponentController extends AdminBaseController
{

    /**
     * 系统组件列表
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

        return new ViewModel([
            'rows' => $rows,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 同步系统组件
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
     * 删除系统组件
     */
    public function deleteAction()
    {
        $component_class = urldecode($this->params()->fromRoute('key'));
        $componentManager = $this->getComponentManager();
        $component = $componentManager->getComponent($component_class);

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
     * 组件接口列表
     */
    public function actionsAction()
    {
        $component_class = urldecode($this->params()->fromRoute('key'));
        $component = $this->getComponentManager()->getComponent($component_class);


        $viewModel = new ViewModel();
        $viewModel->setVariables([
            'component' => $component,
        ]);
        $viewModel->setTerminal(true);
        return $viewModel;
    }


    /**
     * 删除组件接口
     */
    public function removeAction()
    {
        $action_id = $this->params()->fromRoute('key');
        $componentManager = $this->getComponentManager();
        $action = $componentManager->getAction($action_id);

        // Clean the acl
        $this->getAclManager()->removeAction($action_id);

        // Delete the action
        $componentManager->removeEntity($action);

        return new JsonModel(['success' => true]);
    }



    /**
     *  ACL 登记
     *
     * @return array
     */
    public static function ComponentRegistry()
    {
        $item = self::CreateControllerRegistry(__CLASS__, '系统组件管理', 'admin/component', 1, 'cubes', 0);

        $item['actions']['index'] = self::CreateActionRegistry('index', '系统组件列表', 1, 'bars', 0);

        $item['actions']['sync'] = self::CreateActionRegistry('sync', '同步系统组件');
        $item['actions']['delete'] = self::CreateActionRegistry('delete', '删除系统组件');
        $item['actions']['actions'] = self::CreateActionRegistry('actions', '组件接口列表');
        $item['actions']['remove'] = self::CreateActionRegistry('remove', '删除组件接口');

        return $item;
    }


}