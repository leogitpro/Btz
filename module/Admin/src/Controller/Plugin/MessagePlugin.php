<?php
/**
 * Simple show page message plugin
 *
 * User: leo
 */

namespace Admin\Controller\Plugin;


use Admin\Controller\IndexController;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class MessagePlugin extends AbstractPlugin
{
    public function show($title = 'Information', $message = '...', $url_href = '', $url_title = '', $delay = null)
    {
        return $this->getController()->forward()->dispatch(IndexController::class, [
            'controller' => IndexController::class, // Fix module listener lost controller name bug.
            'action' => 'message',
            'title' => $title,
            'content' => $message,
            'url_href' => $url_href,
            'url_title' => $url_title,
            'delay' => $delay,
        ]);
    }
}