<?php
/**
 * Module.php
 *
 * User Module class
 *
 */

namespace User;


use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\MvcEvent;


class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

}