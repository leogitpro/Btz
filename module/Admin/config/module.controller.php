<?php

namespace Admin;

use Zend\ServiceManager\Factory\InvokableFactory;


return [
    'factories' => [
        Controller\IndexController::class => InvokableFactory::class,
    ],
];