<?php
/**
* AclController.php
*
* @author: Leo <camworkster@gmail.com>
* @version: 1.0
*/


namespace Admin\Controller;


class AclController extends BaseController
{

    public function autoRegisterComponent()
    {
        return [
            'controller' => __CLASS__,
            'name' => 'Access control',
            'route' => 'admin/acl',
            'menu' => true,
            'rank' => 0,
            'icon' => 'cubes',
            'actions' => [
                [
                    'action' => 'member',
                    'name' => 'Members',
                    'menu' => true,
                    'rank' => 0,
                    'icon' => 'users',
                ],
                [
                    'action' => 'department',
                    'name' => 'Departments',
                    'menu' => true,
                    'rank' => 0,
                    'icon' => 'bars',
                ],
                [
                    'action' => 'acl-member',
                    'name' => 'Member access control',
                ],
                [
                    'action' => 'acl-department',
                    'name' => 'Department access control',
                ],
            ],
        ];
    }


    public function memberAction()
    {
        //todo
    }


    public function aclMemberAction()
    {
        //todo
    }



    public function departmentAction()
    {
        //todo
    }


    public function aclDepartmentAction()
    {
        //todo
    }



}