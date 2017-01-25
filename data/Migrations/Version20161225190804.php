<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161225190804 extends AbstractMigration
{

    /**
     * @var string db table name
     */
    private $_table_user = 'user';

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Add index for active token and password reset token';
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

        $table->addIndex(['active_token'], 'index_active_token');
        $table->addIndex(['pwd_reset_token'], 'index_pwd_reset_token');
    }


    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $table = $schema->getTable('user');
        $table->dropIndex('index_active_token');
        $table->dropIndex('index_pwd_reset_token');

        $schema->dropTable($this->_table_user);
    }
}
