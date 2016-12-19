<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161214141615 extends AbstractMigration
{

    /**
     * @var string db table adminer name
     */
    private $_table_user = 'user';


    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Create user table';
    }


    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable($this->_table_user);
        $table->addColumn('uid', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $table->addColumn('email', 'string', ['length' => 45, 'comment' => '用户邮件地址, 登入账号.']);
        $table->addColumn('passwd', 'string', ['length' => 32, 'fixed' => true, 'comment' => '用户密码.']);
        $table->addColumn('name', 'string', ['length' => 45, 'comment' => '用户名称.']);
        $table->addColumn('status', 'smallint', ['unsigned' => true, 'default' => 0, 'comment' => '用户状态.']);
        $table->addColumn('created', 'datetime', ['comment' => '创建时间']);
        $table->addColumn('active_token', 'string', ['length' => 32, 'fixed' => true, 'comment' => '账号注册激活code']);
        $table->addColumn('pwd_reset_token', 'string', ['length' => 32, 'notnull' => false, 'default' => '', 'fixed' => true, 'comment' => '密码重置CODE']);
        $table->addColumn('pwd_reset_token_created', 'integer', ['unsigned' => true, 'notnull' => false, 'default' => 0, 'comment' => '重置有效开始时间']);
        $table->setPrimaryKey(['uid']);
        $table->addUniqueIndex(array('email'));
    }


    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable($this->_table_user);
    }
}
