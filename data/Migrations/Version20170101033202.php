<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170101033202 extends AbstractMigration
{

    public function getDescription()
    {
        return 'Create system administrator tables';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        $member = $schema->createTable('sys_member'); // Table name: sys_member

        // Setting a global in connection configuration. use configuration mode.
        /**
        $table->addOption('engine', 'InnoDB');
        $table->addOption('charset', 'utf8mb4');
        $table->addOption('collate', 'utf8mb4_unicode_ci');
        //*/

        $member->addColumn('member_id', 'string', ['fixed' => true, 'length' => 36]);
        $member->addColumn('member_email', 'string', ['length' => 45, 'default' => '']);
        $member->addColumn('member_password', 'string', ['length' => 32, 'fixed' => true, 'default' => '']);
        $member->addColumn('member_active_code', 'string', ['length' => 32, 'fixed' => true, 'default' => '']);
        $member->addColumn('member_name', 'string', ['length' => 45, 'default' => '']);
        $member->addColumn('member_status', 'smallint', ['default' => 0, 'comment' => 'Account status']);
        $member->addColumn('member_level', 'smallint', ['default' => 0, 'comment' => 'Account level']);
        $member->addColumn('member_expired', 'datetime');
        $member->addColumn('member_created', 'datetime');
        $member->setPrimaryKey(['member_id']);
        $member->addUniqueIndex(['member_email']);
        $member->addIndex(['member_active_code']);
        $member->addIndex(['member_status', 'member_level', 'member_name']);


        $department = $schema->createTable('sys_department');
        $department->addColumn('dept_id', 'string', ['fixed' => true, 'length' => 36]);
        $department->addColumn('dept_name', 'string', ['length' => 45, 'default' => '']);
        $department->addColumn('dept_status', 'smallint', ['default' => 0]);
        $department->addColumn('dept_created', 'datetime');
        $department->setPrimaryKey(['dept_id']);
        $department->addUniqueIndex(['dept_name']);
        $department->addIndex(['dept_status']);
        $department->addIndex(['dept_status', 'dept_name']);


        $departmentMember = $schema->createTable('sys_department_member');
        $departmentMember->addColumn('dept', 'string', ['fixed' => true, 'length' => 36]);
        $departmentMember->addColumn('member', 'string', ['fixed' => true, 'length' => 36]);
        $departmentMember->setPrimaryKey(['dept', 'member']);


        $controller = $schema->createTable('sys_controller');
        $controller->addColumn('controller_class', 'string', ['length' => 100, 'default' => '']);
        $controller->addColumn('controller_name', 'string', ['length' => 100, 'default' => '']);
        $controller->addColumn('controller_icon', 'string', ['length' => 45, 'default' => '']);
        $controller->addColumn('controller_route', 'string', ['length' => 45, 'default' => '']);
        $controller->addColumn('controller_rank', 'integer', ['unsigned' => true, 'default' => 0]);
        $controller->addColumn('controller_menu', 'smallint', ['unsigned' => true, 'default' => 0]);
        $controller->setPrimaryKey(['controller_class']);
        $controller->addIndex(['controller_rank', 'controller_name']);
        $controller->addIndex(['controller_rank', 'controller_menu', 'controller_name']);


        $action = $schema->createTable('sys_action');
        $action->addColumn('action_id', 'string', ['fixed' => true, 'length' => 36]);
        $action->addColumn('controller_key', 'string', ['length' => 100, 'default' => '']);
        $action->addColumn('action_key', 'string', ['length' => 45, 'default' => '']);
        $action->addColumn('action_name', 'string', ['length' => 45, 'default' => '']);
        $action->addColumn('action_icon', 'string', ['length' => 45, 'default' => '']);
        $action->addColumn('action_rank', 'integer', ['unsigned' => true, 'default' => 0]);
        $action->addColumn('action_menu', 'smallint', ['unsigned' => true, 'default' => 0]);
        $action->setPrimaryKey(['action_id']);
        $action->addIndex(['action_menu']);
        $action->addIndex(['action_rank', 'action_name']);
        $action->addIndex(['action_rank']);
        $action->addIndex(['controller_key']);
        $action->addIndex(['action_rank', 'action_menu', 'action_name']);
        $action->addIndex(['controller_key', 'action_menu']);
        $action->addIndex(['controller_key', 'action_key']);


        $aclMember = $schema->createTable('sys_acl_member');
        $aclMember->addColumn('action', 'string', ['fixed' => true, 'length' => 36]);
        $aclMember->addColumn('member', 'string', ['fixed' => true, 'length' => 36]);
        $aclMember->addColumn('status', 'smallint', ['default' => 0]);
        $aclMember->setPrimaryKey(['action', 'member']);
        $aclMember->addIndex(['member']);
        $aclMember->addIndex(['action']);


        $aclDepartment = $schema->createTable('sys_acl_department');
        $aclDepartment->addColumn('action', 'string', ['fixed' => true, 'length' => 36]);
        $aclDepartment->addColumn('dept', 'string', ['fixed' => true, 'length' => 36]);
        $aclDepartment->addColumn('status', 'smallint', ['default' => 0]);
        $aclDepartment->setPrimaryKey(['action', 'dept']);
        $aclDepartment->addIndex(['dept']);
        $aclDepartment->addIndex(['action']);

    }


    public function postUp(Schema $schema)
    {
        parent::postUp($schema);

        $this->connection->executeUpdate(
            "INSERT INTO `sys_department` (`dept_id`, `dept_name`, `dept_status`, `dept_created`) VALUES (?, ?, ?, ?)",
            ['ad739904-f423-11e6-b154-acbc32bf6185', 'Default', 1, date('Y-m-d H:i:s')]
        );
        $this->connection->executeUpdate(
            "INSERT INTO `sys_department` (`dept_id`, `dept_name`, `dept_status`, `dept_created`) VALUES (?, ?, ?, ?)",
            ['266cb0b4-2022-11e7-a2ce-acbc32bf6185', 'WeChat Group', 1, date('Y-m-d H:i:s')]
        );

        // Init member table
        $this->connection->executeUpdate(
            'INSERT INTO `sys_member` (`member_id`, `member_email`, `member_password`, `member_name`, `member_status`, `member_level`, `member_expired`, `member_created`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', //Sql
            ['be152a3e-f423-11e6-a4a4-acbc32bf6185', 'admin@example.com', md5('1212'), 'Administrator', 1, 9, '2099-12-30 23:59:59', date('Y-m-d H:i:s')]
        );

        // Init department with member table
        $this->connection->executeUpdate(
            'INSERT INTO `sys_department_member` (`dept`, `member`) VALUES (?, ?)',
            ['ad739904-f423-11e6-b154-acbc32bf6185', 'be152a3e-f423-11e6-a4a4-acbc32bf6185']
        );

    }



    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('sys_member');
        $schema->dropTable('sys_department');
        $schema->dropTable('sys_department_member');
        $schema->dropTable('sys_controller');
        $schema->dropTable('sys_action');
        $schema->dropTable('sys_acl_member');
        $schema->dropTable('sys_acl_department');
    }
}
