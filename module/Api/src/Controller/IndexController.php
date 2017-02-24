<?php
/**
 * IndexController.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Api\Controller;


class IndexController extends ApiBaseController
{

    public function indexAction()
    {
        echo date('c');
    }

}