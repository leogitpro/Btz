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



    public static function ComponentRegistryX()
    //public function autoRegisterComponent()
    {
        return [
            'controller' => __CLASS__,
            'name' => '站内消息',
            'route' => 'admin/message',
            'menu' => true,
            'rank' => 20,
            'icon' => 'envelope-o',
            'actions' => [
                [
                    'action' => 'index',
                    'name' => '收件箱',
                    'menu' => true,
                    'rank' => 6,
                    'icon' => 'envelope-o',
                ],
                [
                    'action' => 'outbox',
                    'name' => '发件箱',
                    'menu' => true,
                    'rank' => 4,
                    'icon' => 'comment-o',
                ],
                [
                    'action' => 'broadcast',
                    'name' => '站内广播',
                    'menu' => true,
                    'rank' => 2,
                    'icon' => 'bullhorn',
                ],
            ],
        ];
    }

    public function onDispatch(MvcEvent $e)
    {

        $serviceManager = $e->getApplication()->getServiceManager();
        $this->messageManager = $serviceManager->get(MessageManager::class);
        $this->deptManager = $serviceManager->get(DepartmentManager::class);
        $this->memberManager = $serviceManager->get(MemberManager::class);

        return parent::onDispatch($e);
    }


    public function indexAction()
    {
        $currentMember = $this->memberManager->getCurrentMember();
        $memberId = (null == $currentMember) ? 0 : $currentMember->getMemberId();

        // Page configuration
        $size = 1;
        $page = (int)$this->params()->fromRoute('key', 1);
        if ($page < 1) { $page = 1; }
        $count = $this->messageManager->getInBoxMessagesCount($memberId);

        // Get pagination helper
        $viewHelperManager = $this->getEvent()->getApplication()->getServiceManager()->get('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        // Configuration pagination
        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/message', ['action' => 'index', 'key' => '%d']));

        $rows = $this->messageManager->getInBoxMessagesByLimitPage($memberId, $page, $size);

        return new ViewModel([
            'rows' => $rows,
            'activeId' => __METHOD__,
        ]);
    }


    public function personalAction()
    {
        //todo
    }


    public function broadcastAction()
    {

        $form = new MessageForm();

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {

                $data = $form->getData();

                $topic = $data['topic'];
                $content = $data['content'];
                $receiver = $this->deptManager->getDepartmentAllMemberIds(Department::DEFAULT_DEPT_ID);

                $currentMember = $this->memberManager->getCurrentMember();
                $sender = 0;
                if (null !== $currentMember) {
                    $sender = $currentMember->getMemberId();
                }

                $this->messageManager->createNewMessage($topic, $content, $sender, $receiver, MessageBox::MESSAGE_TYPE_BROADCAST);

                return $this->getMessagePlugin()->show(
                    'Broadcast published',
                    'The new broadcast has been published success!',
                    $this->url()->fromRoute('admin/message'),
                    'Messages',
                    3
                );
            }
        }

        return new ViewModel([
            'form' => $form,
            'activeId' => __METHOD__,
        ]);
    }


}