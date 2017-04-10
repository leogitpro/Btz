<?php
/**
 * FormLocalMessage.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Form\View\Helper;


use Zend\Form\ElementInterface;
use Zend\View\Helper\AbstractHelper;


class FormLocalMessage extends AbstractHelper
{

    public function __invoke(ElementInterface $element, $messages = [])
    {
        if (empty($messages)) {
            return ;
        }

        $defaultMessages = $element->getMessages();
        if(empty($defaultMessages)) {
            return ;
        }

        if(array_key_exists('__undefined__', $messages)) {
            $keys = array_keys($defaultMessages);
            foreach ($keys as $key) {
                if(array_key_exists($key, $messages)) {
                    $defaultMessages[$key] = $messages[$key];
                } else {
                    unset($defaultMessages[$key]);
                }
            }
            if(empty($defaultMessages)) {
                $defaultMessages['__undefined__'] = $messages['__undefined__'];
            }
        } else {
            $keys = array_keys($defaultMessages);
            foreach ($keys as $key) {
                if(array_key_exists($key, $messages)) {
                    $defaultMessages[$key] = $messages[$key];
                }
            }
        }
        $element->setMessages($defaultMessages);
    }
}