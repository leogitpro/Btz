<?php
/**
 * MessageController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Controller;



use Admin\Entity\Department;
use Admin\Entity\MessageBox;
use Admin\Form\MessageForm;
use Admin\Service\DepartmentManager;
use Admin\Service\MemberManager;
use Admin\Service\MessageManager;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class MessageController extends BaseController
{

    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * @var DepartmentManager
     */
    private $deptManager;


    /**
     * @var MemberManager
     */
    private $memberManager;


    public function onDispatch(MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();

        $this->messageManager = $serviceManager->get(MessageManager::class);
        $this->deptManager = $serviceManager->get(DepartmentManager::class);
        $this->memberManager = $serviceManager->get(MemberManager::class);

        return parent::onDispatch($e);
    }


    /**
     * 全部消息清单. For supper administrator check
     */
    public function indexAction()
    {
        // Page configuration
        $size = 10;
        $page = (int)$this->params()->fromRoute('key', 1);
        if ($page < 1) { $page = 1; }
        $count = $this->messageManager->getMessageContentsCount();

        // Get pagination helper
        $viewHelperManager = $this->getEvent()->getApplication()->getServiceManager()->get('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/message', ['action' => 'index', 'key' => '%d']));

        $rows = $this->messageManager->getMessageContentsByLimitPage($page, $size);

        return new ViewModel([
            'rows' => $rows,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 收件箱
     */
    public function inAction()
    {
        // Page configuration
        $size = 10;
        $page = (int)$this->params()->fromRoute('key', 1);
        if ($page < 1) { $page = 1; }
        $count = $this->messageManager->getInBoxMessagesCount();

        // Get pagination helper
        $viewHelperManager = $this->getEvent()->getApplication()->getServiceManager()->get('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/message', ['action' => 'in', 'key' => '%d']));

        $rows = $this->messageManager->getInBoxMessagesByLimitPage($page, $size);

        return new ViewModel([
            'rows' => $rows,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 读消息
     */
    public function readAction()
    {
        $result = ['success' => false, 'code' => 0, 'message' => ''];

        $boxId = $this->params()->fromRoute('key');

        $messageBox = $this->messageManager->getMessageBox($boxId);
        if (null == $messageBox) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '无效的消息编号:' . $boxId);
            return ;
        }

        $messageBox->setReceiverStatus(MessageBox::STATUS_RECEIVER_READ);
        $this->messageManager->saveModifiedEntity($messageBox);

        $result['success'] = true;
        $result['message'] = 'Message has read.';

        return new JsonModel($result);
    }


    /**
     * 删除消息
     */
    public function deleteAction()
    {

        $result = ['success' => false, 'code' => 0, 'message' => ''];

        $boxId = $this->params()->fromRoute('key');

        $messageBox = $this->messageManager->getMessageBox($boxId);
        if (null == $messageBox) {
            $this->getResponse()->setStatusCode(404);
            $this->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . '无效的消息编号:' . $boxId);
            return ;
        }

        $member = $this->memberManager->getCurrentMember();
        if ($member->getMemberId() == $messageBox->getReceiver()) {
            $messageBox->setReceiverStatus(MessageBox::STATUS_RECEIVER_DELETED);
        }
        if ($member->getMemberId() == $messageBox->getSender()) {
            $messageBox->setSenderStatus(MessageBox::STATUS_SENDER_DELETED);
        }

        $this->messageManager->saveModifiedEntity($messageBox);

        $result['success'] = true;
        $result['message'] = 'Message has deleted.';

        return new JsonModel($result);
    }



    /**
     * 发件箱
     */
    public function outAction()
    {
        // Page configuration
        $size = 1;
        $page = (int)$this->params()->fromRoute('key', 1);
        if ($page < 1) { $page = 1; }
        $count = $this->messageManager->getOutBoxMessagesCount();

        // Get pagination helper
        $viewHelperManager = $this->getEvent()->getApplication()->getServiceManager()->get('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/message', ['action' => 'out', 'key' => '%d']));

        $rows = $this->messageManager->getOutBoxMessagesByLimitPage($page, $size);

        return new ViewModel([
            'rows' => $rows,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 发消息
     */
    public function sendAction()
    {
        //todo
    }



    /**
     * 广播消息
     */
    public function broadcastAction()
    {

        $form = new MessageForm();

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $this->messageManager->broadcastMessage($data['topic'], $data['content']);

                return $this->getMessagePlugin()->show(
                    '广播已发送',
                    '广播消息已经群发!',
                    $this->url()->fromRoute('admin/message'),
                    '返回',
                    3
                );
            }
        }

        return new ViewModel([
            'form' => $form,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * Controller and actions registry
     *
     * @return array
     */
    public static function ComponentRegistry()
    {
        $item = self::CreateControllerRegistry(__CLASS__, '站内消息', 'admin/message', 1, 'envelope-o', 20);

        $item['actions']['index'] = self::CreateActionRegistry('index', '全部消息', 1, 'envelope-o', 0);
        $item['actions']['in'] = self::CreateActionRegistry('in', '收件箱', 1, 'envelope-o', 8);
        $item['actions']['out'] = self::CreateActionRegistry('out', '发件箱', 1, 'envelope-o', 6);
        $item['actions']['send'] = self::CreateActionRegistry('send', '发消息', 1, 'envelope-o', 4);
        $item['actions']['broadcast'] = self::CreateActionRegistry('broadcast', '发广播', 1, 'bullhorn', 1);

        return $item;
    }

}