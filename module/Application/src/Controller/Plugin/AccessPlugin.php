<?php
/**
 * Created by PhpStorm.
 * User: leo
 * Date: 16/9/21
 * Time: PM6:05
 */

namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;


class AccessPlugin extends AbstractPlugin
{

    public function checkAccess($action_name)
    {
        var_dump(get_class($this->getController()));
        return false;
    }

}