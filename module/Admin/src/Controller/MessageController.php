<?php
/**
 * MessageController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Controller;



use Zend\View\Model\ViewModel;

class MessageController extends BaseController
{
    public function autoRegisterComponent()
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
                    'action' => 'personal',
                    'name' => '发送私信',
                    'menu' => true,
                    'rank' => 4,
                    'icon' => 'comment-o',
                ],
                [
                    'action' => 'broadcast',
                    'name' => '发送广播',
                    'menu' => true,
                    'rank' => 2,
                    'icon' => 'bullhorn',
                ],
            ],
        ];
    }



    public function indexAction()
    {
        //todo
    }

    public function personalAction()
    {
        //todo
    }


    public function broadcastAction()
    {
        return new ViewModel([
            'activeId' => __METHOD__,
        ]);
    }


}