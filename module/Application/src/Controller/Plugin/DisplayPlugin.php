<?php
/**
 * Display plugin
 *
 * These plugin need forward plugin support
 *
 * User: leo
 */


namespace Application\Controller\Plugin;


use Application\Controller\DisplayController;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;


class DisplayPlugin extends AbstractPlugin
{
    public function show($title = 'Information', $message = '...', $url_href = '', $url_title = '', $delay = null)
    {
        return $this->getController()->forward()->dispatch(DisplayController::class, [
            'controller' => DisplayController::class, // Fix module listener lost controller name bug.
            'action' => 'message',
            'title' => $title,
            'content' => $message,
            'url_href' => $url_href,
            'url_title' => $url_title,
            'delay' => $delay,
        ]);
    }
}