<?php
/**
 * FeedbackController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Admin\Controller;


use Admin\Entity\Feedback;
use Admin\Exception\RuntimeException;
use Admin\Form\FeedbackForm;
use Ramsey\Uuid\Uuid;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;


/**
 * 信息反馈管理
 *
 * Class FeedbackController
 * @package Admin\Controller
 */
class FeedbackController extends AdminBaseController
{


    /**
     * 我的反馈列表
     */
    public function indexAction()
    {

        $viewHelperManager = $this->getSm('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        $page = (int)$this->params()->fromRoute('key', 1);
        if ($page < 1) {
            $page = 1;
        }

        $myself = $this->getMemberManager()->getCurrentMember();
        $feedbackManager = $this->getFeedbackManager();

        $size = 5;
        $count = $feedbackManager->getMemberFeedbackCount($myself);

        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/feedback', ['action' => 'index', 'key' => '%d']));

        $rows = $feedbackManager->getMemberFeedbackByLimitPage($myself, $page, $size);

        return new ViewModel([
            'rows' => $rows,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 新增我的反馈
     */
    public function addAction()
    {
        $form = new FeedbackForm();

        if($this->getRequest()->isPost()) {

            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {
                $data = $form->getData();

                $myself = $this->getMemberManager()->getCurrentMember();
                $feedback = new Feedback();
                $feedback->setId(Uuid::uuid1()->toString());
                $feedback->setContent($data['content']);
                $feedback->setCreated(new \DateTime());
                $feedback->setUpdated(new \DateTime());
                $feedback->setReply('');
                $feedback->setSender($myself);
                $feedback->setReplier($myself);
                $this->getFeedbackManager()->saveModifiedEntity($feedback);

                return $this->go(
                    '反馈已接收',
                    '感谢您宝贵的反馈意见! 我们已将您的意见分发到相关的负责人, 他们收到后会尽快进行回应. 谢谢!',
                    $this->url()->fromRoute('admin/feedback')
                );
            }
        }

        return new ViewModel([
            'form' => $form,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 删除我的反馈
     */
    public function deleteAction()
    {
        $feedbackId = (string)$this->params()->fromRoute('key');

        $feedbackManager = $this->getFeedbackManager();
        $feedback = $feedbackManager->getFeedback($feedbackId);
        $myself = $this->getMemberManager()->getCurrentMember();

        if ($myself->getMemberId() != $feedback->getSender()->getMemberId()) {
            throw new RuntimeException('你不能删除别人的反馈信息!');
        }

        $feedbackManager->removeEntity($feedback);

        return $this->go(
            '反馈已删除',
            '您已经删除您的反馈意见! 如果有任何想法, 请让我们知道. 谢谢!',
            $this->url()->fromRoute('admin/feedback')
        );
    }


    /**
     * 全部反馈列表
     */
    public function allAction()
    {
        $viewHelperManager = $this->getSm('ViewHelperManager');
        $paginationHelper = $viewHelperManager->get('pagination');

        $page = (int)$this->params()->fromRoute('key', 1);
        if ($page < 1) {
            $page = 1;
        }

        $feedbackManager = $this->getFeedbackManager();

        $size = 5;
        $count = $feedbackManager->getAllFeedbackCount();

        $paginationHelper->setPage($page);
        $paginationHelper->setSize($size);
        $paginationHelper->setCount($count);
        $paginationHelper->setUrlTpl($this->url()->fromRoute('admin/feedback', ['action' => 'index', 'key' => '%d']));

        $rows = $feedbackManager->getFeedbackByLimitPage($page, $size);

        return new ViewModel([
            'rows' => $rows,
            'activeId' => __METHOD__,
        ]);
    }


    /**
     * 删除反馈
     */
    public function closeAction()
    {
        $feedbackId = (string)$this->params()->fromRoute('key');

        $feedbackManager = $this->getFeedbackManager();
        $feedback = $feedbackManager->getFeedback($feedbackId);

        $feedbackManager->removeEntity($feedback);

        return $this->go(
            '反馈已关闭',
            '成员的反馈已经关闭!',
            $this->url()->fromRoute('admin/feedback', ['action' => 'all'])
        );
    }


    /**
     * 回应反馈
     */
    public function replyAction()
    {
        $feedbackId = (string)$this->params()->fromRoute('key');

        $feedbackManager = $this->getFeedbackManager();

        $feedback = $feedbackManager->getFeedback($feedbackId);

        $myself = $this->getMemberManager()->getCurrentMember();

        $content = strip_tags($this->params()->fromPost('content', ''));

        $feedback->setReplier($myself);
        $feedback->setReply($content);
        $feedback->setUpdated(new \DateTime());

        $feedbackManager->saveModifiedEntity($feedback);

        return new JsonModel([
            'success' => true,
            'code' => 0,
            'message' => '',
        ]);
    }



    /**
     *  ACL 登记
     *
     * @return array
     */
    public static function ComponentRegistry()
    {
        $item = self::CreateControllerRegistry(__CLASS__, '信息反馈管理', 'admin/feedback', 1, 'commenting-o', 12);

        $item['actions']['index'] = self::CreateActionRegistry('index', '我的反馈列表', 1, 'comments-o', 9);
        $item['actions']['add'] = self::CreateActionRegistry('add', '新增我的反馈', 1, 'comment-o', 1);

        $item['actions']['delete'] = self::CreateActionRegistry('delete', '删除我的反馈');

        $item['actions']['all'] = self::CreateActionRegistry('all', '全部反馈列表', 1, 'comments-o', 10);
        $item['actions']['close'] = self::CreateActionRegistry('close', '删除反馈');
        $item['actions']['reply'] = self::CreateActionRegistry('reply', '回应反馈');

        return $item;
    }


}