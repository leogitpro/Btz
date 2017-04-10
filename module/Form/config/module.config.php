<?php
/**
 * module.config.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Form;


use Zend\ServiceManager\Factory\InvokableFactory;


return [
    'view_helpers' => [
        'factories' => [
            View\Helper\FormLocalMessage::class => InvokableFactory::class,
            \Zend\Form\View\Helper\FormElementErrors::class => View\Helper\Factory\FormElementErrorsFactory::class,
        ],
        'aliases' => [
            'formLocalMessage' => View\Helper\FormLocalMessage::class,
        ],
    ],
];