<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161114165551 extends AbstractMigration
{
    /**
     * @var string db table adminer name
     */
    private $_table_adminer = 'sys_adminer';


    public function getDescription()
    {
        $desc = 'Create administrator member table';
        return $desc;
    }


    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        $table = $schema->createTable($this->_table_adminer);

        /**  Setting a global in connection configuration.
        $table->addOption('engine', 'InnoDB');
        $table->addOption('charset', 'utf8mb4');
        $table->addOption('collate', 'utf8mb4_unicode_ci');
        //*/

        $table->addColumn('admin_id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $table->addColumn('admin_email', 'string', ['length' => 45, 'default' => '', 'comment' => '用户邮件地址, 登入账号.']);
        $table->addColumn('admin_passwd', 'string', ['length' => 32, 'default' => '', 'fixed' => true, 'comment' => '用户密码.']);
        $table->addColumn('admin_name', 'string', ['length' => 45, 'default' => '', 'comment' => '用户名称.']);
        $table->addColumn('admin_status', 'smallint', ['unsigned' => true, 'default' => 0, 'comment' => '用户状态.']);
        $table->setPrimaryKey(['admin_id']);

        /** Error call
        $this->addSql(
            "INSERT INTO `adminer` (`admin_email`, `admin_passwd`, `admin_name`, `admin_status`) VALUES (?, ?, ?, ?)",
            ['leo@email.com', strtolower(md5('12345')), 'Leo', 1]
        );
        //*/

        //print_r($schema->toSql($this->platform));
    }


    /**
     * @param Schema $schema
     */
    public function postUp(Schema $schema)
    {
        /**
        //默认管理员设置
        $this->connection->executeQuery(
        //$this->addSql(
            "INSERT INTO `adminer` (`admin_email`, `admin_passwd`, `admin_name`, `admin_status`) VALUES (?, ?, ?, ?)",
            ['leo@email.com', strtolower(md5('12345')), 'Leo', 1]
        );
        //*/
    }


    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable($this->_table_adminer);
    }
}
