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


/**
 * 系统消息管理
 *
 * Class MessageController
 * @package Admin\Controller
 */
class MessageController extends AdminBaseController
{

    /**
     * 全站消息列表
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
     * 删除系统消息
     */
    public function closeAction()
    {
        $result = ['success' => false, 'code' => 0, 'message' => 'Invalid message ID'];

        $messageId = (string)$this->params()->fromRoute('key');

        $messageManager = $this->getMessageManager();
        $message = $messageManager->getMessageContent($messageId);

        $message->setStatus(MessageContent::STATUS_INVALID);
        $messageManager->saveModifiedEntity($message);

        $result['success'] = true;
        $result['message'] = 'Closed message: ' . $message->getTopic();

        return new JsonModel($result);
    }


    /**
     * 我的收件箱
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
     * 标记消息已读
     */
    public function readAction()
    {
        $result = ['success' => false, 'code' => 0, 'message' => 'Invalid message ID'];

        $boxId = $this->params()->fromRoute('key');

        $messageManager = $this->getMessageManager();
        $messageBox = $messageManager->getMessageBox($boxId);

        $messageBox->setReceiverStatus(MessageBox::STATUS_RECEIVER_READ);
        $messageManager->saveModifiedEntity($messageBox);

        $result['success'] = true;
        $result['message'] = 'Message has read.';

        return new JsonModel($result);
    }


    /**
     * 删除我的消息
     */
    public function deleteAction()
    {

        $result = ['success' => false, 'code' => 0, 'message' => 'Invalid message ID'];

        $boxId = $this->params()->fromRoute('key');

        $messageManager = $this->getMessageManager();
        $messageBox = $messageManager->getMessageBox($boxId);

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
     * 我的未读消息
     */
    public function unreadAction()
    {
        $list = ['success' => true, 'count' => 0, 'rows' => []];

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
     * 我的发件箱
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
     * 发消息给成员
     */
    public function sendAction()
    {
        $key = (string)$this->params()->fromRoute('key');

        $memberManager = $this->getMemberManager();
        $receiver = $memberManager->getMember($key);

        $form = new MessageForm($receiver, $memberManager);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {
                $data = $form->getData();

                if (null == $receiver || $receiver->getMemberId() != $data['receiver_id']) {
                    $receiver = $memberManager->getMember($data['receiver_id']);
                }

                $this->getMessageManager()->sendOneMessage($receiver, $data['topic'], $data['content']);

                return $this->go(
                    '消息已发送',
                    '您给 ' . $data['receiver_name'] . ' 的消息已经发送!',
                    $this->url()->fromRoute('admin/message', ['action' => 'out'])
                );
            }
        }

        return new ViewModel([
            'form' => $form,
            'receiver' => $receiver,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 发消息给群组
     */
    public function deptAction()
    {
        $key = (string)$this->params()->fromRoute('key');

        $deptManager = $this->getDeptManager();
        $receiver = $deptManager->getDepartment($key);

        $form = new MessageForm($receiver, $deptManager);

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();
                $dept = $deptManager->getDepartment($data['receiver_id']);
                $this->getMessageManager()->broadcastMessage($dept, $data['topic'], $data['content']);

                return $this->go(
                    '消息已发送',
                    '分组消息 ' . $data['receiver_name'] . ' 已经群发!',
                    $this->url()->fromRoute('admin/message')
                );
            }
        }

        return new ViewModel([
            'form' => $form,
            'receiver' => $receiver,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 广播全体消息
     */
    public function broadcastAction()
    {

        $form = new MessageForm('*');

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();
                $dept = $this->getDeptManager()->getDefaultDepartment();
                $this->getMessageManager()->broadcastMessage($dept, $data['topic'], $data['content']);

                return $this->go(
                    '广播已发送',
                    '广播消息已经群发!',
                    $this->url()->fromRoute('admin/message')
                );
            }
        }

        return new ViewModel([
            'form' => $form,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     *  ACL 登记
     *
     * @return array
     */
    public static function ComponentRegistry()
    {
        $item = self::CreateControllerRegistry(__CLASS__, '系统消息管理', 'admin/message', 1, 'envelope-o', 8);

        $item['actions']['index'] = self::CreateActionRegistry('index', '全站消息列表', 1, 'envelope-o', 0);
        $item['actions']['in'] = self::CreateActionRegistry('in', '我的收件箱', 1, 'envelope-o', 8);
        $item['actions']['out'] = self::CreateActionRegistry('out', '我的发件箱', 1, 'envelope-o', 6);
        $item['actions']['broadcast'] = self::CreateActionRegistry('broadcast', '广播全体消息', 1, 'bullhorn', 1);

        $item['actions']['send'] = self::CreateActionRegistry('send', '发消息给成员');
        $item['actions']['dept'] = self::CreateActionRegistry('dept', '发消息给群组');
        $item['actions']['close'] = self::CreateActionRegistry('close', '删除系统消息');
        $item['actions']['read'] = self::CreateActionRegistry('read', '标记消息已读');
        $item['actions']['delete'] = self::CreateActionRegistry('delete', '删除我的消息');
        $item['actions']['unread'] = self::CreateActionRegistry('unread', '我的未读消息');


        return $item;
    }

}