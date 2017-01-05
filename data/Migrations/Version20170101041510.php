<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170101041510 extends AbstractMigration
{

    public function getDescription()
    {
        return 'Init member and department data';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        // Init department table
        $this->addSql(
            'INSERT INTO `sys_department` (`dept_id`, `dept_name`, `dept_members`, `dept_status`, `dept_created`) VALUES (?, ?, ?, ?, ?)',
            [1, 'Default', 0, 1, date('Y-m-d H:i:s')],
            ['integer', 'string', 'smallint', 'string']
        );

        // Init member table
        $this->addSql(
            'INSERT INTO `sys_member` (`member_id`, `member_email`, `member_password`, `member_name`, `member_status`, `member_level`, `member_created`) VALUES (?, ?, ?, ?, ?, ?, ?)', //Sql
            [1, 'admin@example.com', md5('admin'), 'Administrator', 1, 9, date('Y-m-d H:i:s')], // Params
            ['integer', 'string', 'string', 'string', 'smallint', 'smallint', 'string'] // Types
        );

        // Init department with member table
        $this->addSql(
            'INSERT INTO `sys_department_member` (`id`, `dept_id`, `member_id`, `status`, `created`) VALUES (?, ?, ?, ?, ?)',
            [1, 1, 1, 1, date('Y-m-d H:i:s')],
            ['integer', 'integer', 'integer', 'smallint', 'string']
        );

        // Update default department members
        $this->addSql(
            'UPDATE `sys_department` SET `dept_members` = `dept_members` + 1 WHERE `dept_id` = 1'
        );

        $count = 0;
        for($i = 2; $i <= 31; $i++) {
            $rank = random_int(1000, 9999);
            $email = 'admin' . $rank . '@test.com';
            $password = md5('1212');
            $name = 'Admin' . $rank;
            $dt = date('Y-m-d H:i:s');
            $this->addSql(
                'INSERT INTO `sys_member` (`member_id`, `member_email`, `member_password`, `member_name`, `member_status`, `member_level`, `member_created`) VALUES (?, ?, ?, ?, ?, ?, ?)', //Sql
                [$i, $email, $password, $name, 0, 0, $dt],
                ['integer', 'string', 'string', 'string', 'smallint', 'smallint', 'string']
            );

            $this->addSql(
                'INSERT INTO `sys_department_member` (`dept_id`, `member_id`, `status`, `created`) VALUES (?, ?, ?, ?)',
                [1, $i, 1, $dt],
                ['integer', 'integer', 'smallint', 'string']
            );
            $count++;
        }
        $this->addSql(
            'UPDATE `sys_department` SET `dept_members` = `dept_members` + ' . $count . ' WHERE `dept_id` = 1'
        );


        for ($i = 2; $i < 12; $i++) {
            $rank = random_int(1000, 9999);
            $name = 'Dept' . $rank;
            $this->addSql(
                'INSERT INTO `sys_department` (`dept_id`, `dept_name`, `dept_members`, `dept_status`, `dept_created`) VALUES (?, ?, ?, ?, ?)',
                [$i, $name, 0, 1, date('Y-m-d H:i:s')],
                ['integer', 'string', 'smallint', 'string']
            );
        }





    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // Delete dept with member relation
        $this->addSql(
            'DELETE FROM `sys_department_member` WHERE `id` = ?',
            [1],
            ['integer']
        );

        // Delete department init data
        $this->addSql(
            'DELETE FROM `sys_department` WHERE `dept_id` = ?',
            [1],
            ['integer']
        );

        // Delete member init data
        $this->addSql(
            'DELETE FROM `sys_member` WHERE `member_id` = ?',
            [1],
            ['integer']
        );
    }
}
