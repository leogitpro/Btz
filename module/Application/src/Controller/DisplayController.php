<?php
/**
 * DisplayController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Application\Controller;


use Zend\View\Model\ViewModel;


class DisplayController extends AppBaseController
{

    /**
     * Display notification message
     *
     * @return ViewModel
     */
    public function messageAction()
    {
        $msg_title = $this->params()->fromRoute('title');
        $msg_content = $this->params()->fromRoute('content');
        $url_href = $this->params()->fromRoute('url_href');
        $url_title = $this->params()->fromRoute('url_title');
        $delay = $this->params()->fromRoute('delay');

        return new ViewModel([
            'msg_title' => $msg_title,
            'msg_content' => $msg_content,
            'url_href' => $url_href,
            'url_title' => $url_title,
            'delay' => $delay,
        ]);
    }
}