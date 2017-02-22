<?php
/**
 * MessageController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Controller;


use Admin\Entity\MessageBox;
use Admin\Entity\MessageContent;
use Admin\Form\MessageForm;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


class MessageController extends AdminBaseController
{

    /**
     * 全部消息清单. For supper administrator check
     */
    public function indexAction()
    {
        // Page configuration
        $size = 10;
        $page = (int)$this->params()->fromRoute('key', 1);
        if ($page < 1) { $page = 1; }

        $messageManager = $this->getMessageManager();
        $count = $messageManager->getMessageContentsCount();

        // Get pagination helper
        $viewHelperManager = $this->getSm('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/message', ['action' => 'index', 'key' => '%d']));

        $rows = $messageManager->getMessageContentsByLimitPage($page, $size);

        return new ViewModel([
            'rows' => $rows,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 关闭某条消息, 影响所有拥有此条消息的人.
     */
    public function closeAction()
    {
        $result = ['success' => false, 'code' => 0, 'message' => 'Invalid message ID'];

        $messageId = (string)$this->params()->fromRoute('key');

        $messageManager = $this->getMessageManager();
        $message = $messageManager->getMessageContent($messageId);
        if (!($message instanceof MessageContent)) {
            return new JsonModel($result);
        }

        $message->setStatus(MessageContent::STATUS_INVALID);
        $messageManager->saveModifiedEntity($message);

        $result['success'] = true;
        $result['message'] = 'Closed message: ' . $message->getTopic();

        return new JsonModel($result);
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

        $messageManager = $this->getMessageManager();
        $count = $messageManager->getInBoxMessagesCount();

        // Get pagination helper
        $viewHelperManager = $this->getSm('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/message', ['action' => 'in', 'key' => '%d']));

        $rows = $messageManager->getInBoxMessagesByLimitPage($page, $size);

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
        $result = ['success' => false, 'code' => 0, 'message' => 'Invalid message ID'];

        $boxId = $this->params()->fromRoute('key');

        $messageManager = $this->getMessageManager();
        $messageBox = $messageManager->getMessageBox($boxId);
        if (null == $messageBox) {
            return new JsonModel($result);
        }

        $messageBox->setReceiverStatus(MessageBox::STATUS_RECEIVER_READ);
        $messageManager->saveModifiedEntity($messageBox);

        $result['success'] = true;
        $result['message'] = 'Message has read.';

        return new JsonModel($result);
    }


    /**
     * 删除消息
     */
    public function deleteAction()
    {

        $result = ['success' => false, 'code' => 0, 'message' => 'Invalid message ID'];

        $boxId = $this->params()->fromRoute('key');

        $messageManager = $this->getMessageManager();
        $messageBox = $messageManager->getMessageBox($boxId);
        if (null == $messageBox) {
            return new JsonModel($result);
        }

        $member = $this->getMemberManager()->getCurrentMember();
        if ($member->getMemberId() == $messageBox->getReceiver()) {
            $messageBox->setReceiverStatus(MessageBox::STATUS_RECEIVER_DELETED);
        }
        if ($member->getMemberId() == $messageBox->getSender()) {
            $messageBox->setSenderStatus(MessageBox::STATUS_SENDER_DELETED);
        }

        $messageManager->saveModifiedEntity($messageBox);

        $result['success'] = true;
        $result['message'] = 'Message has deleted.';

        return new JsonModel($result);
    }


    /**
     * 未读消息
     */
    public function unreadAction()
    {
        $list = ['count' => 0, 'rows' => []];

        $messageManager = $this->getMessageManager();
        $list['count'] = $messageManager->getUnreadMessagesCount();
        $list['inboxUrl'] = $this->url()->fromRoute('admin/message', ['action' => 'in']);
        $rows = $messageManager->getMyLatestMessages(5);
        $messages = [];
        foreach ($rows as $row) {
            if ($row instanceof MessageBox) {
                $messages[] = [
                    'id' => $row->getId(),
                    'topic' => $row->getContent()->getTopic(),
                    'unread' => MessageBox::STATUS_RECEIVER_UNREAD == $row->getReceiverStatus() ? 1 : 0,
                    'time' => $row->getCreated()->format('Y-m-d H:i:s'),
                    'sender' => $row->getSenderName(),
                ];
            }
        }

        $list['rows'] = $messages;

        return new JsonModel($list);
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

        $messageManager = $this->getMessageManager();
        $count = $messageManager->getOutBoxMessagesCount();

        // Get pagination helper
        $viewHelperManager = $this->getSm('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/message', ['action' => 'out', 'key' => '%d']));

        $rows = $messageManager->getOutBoxMessagesByLimitPage($page, $size);

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
        $key = (string)$this->params()->fromRoute('key');

        $memberManager = $this->getMemberManager();
        $receiver = $memberManager->getMember($key);

        $form = new MessageForm($memberManager, $receiver);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {
                $data = $form->getData();

                if (null == $receiver || $receiver->getMemberId() != $data['receiver_id']) {
                    $receiver = $memberManager->getMember($data['receiver_id']);
                }

                $this->getMessageManager()->sendOneMessage($receiver, $data['topic'], $data['content']);

                return $this->getMessagePlugin()->show(
                    '消息已发送',
                    '您给 ' . $data['receiver_name'] . ' 的消息已经发送!',
                    $this->url()->fromRoute('admin/message', ['action' => 'out']),
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
     * 群发消息
     */
    public function deptAction()
    {
        $key = (string)$this->params()->fromRoute('key');

        $deptManager = $this->getDeptManager();
        $receiver = $deptManager->getDepartment($key);

        $form = new MessageForm($deptManager, $receiver);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {
                $data = $form->getData();

                $this->getMessageManager()->broadcastMessage($data['topic'], $data['content'], (string)$data['receiver_id']);

                return $this->getMessagePlugin()->show(
                    '消息已发送',
                    '分组消息 ' . $data['receiver_name'] . ' 已经群发!',
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
     * 广播消息
     */
    public function broadcastAction()
    {

        $form = new MessageForm(null, '*');

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $this->getMessageManager()->broadcastMessage($data['topic'], $data['content']);

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
        $item['actions']['out'] = self::CreateActionRegistry('out', '发件箱', 0, 'envelope-o', 6);
        $item['actions']['send'] = self::CreateActionRegistry('send', '发消息', 0, 'envelope-o', 4);
        $item['actions']['dept'] = self::CreateActionRegistry('dept', '群发消息', 1, 'envelope-o', 3);
        $item['actions']['broadcast'] = self::CreateActionRegistry('broadcast', '发广播', 1, 'bullhorn', 1);


        return $item;
    }

}