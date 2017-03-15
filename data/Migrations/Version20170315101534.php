<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170315101534 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('app_contact');
        $table->addColumn('id', 'string', ['fixed' => true, 'length' => 36]);
        $table->addColumn('email', 'string', ['length' => 45, 'default' => '', 'comment' => '用户邮件地址']);
        $table->addColumn('subject', 'string', ['length' => 128, 'default' => '', 'comment' => 'Subject']);
        $table->addColumn('content', 'text', ['default' => '', 'notnull' => false, 'comment' => 'Message content']);
        $table->addColumn('status', 'smallint', ['unsigned' => true, 'default' => 0, 'comment' => 'message read status']);
        $table->addColumn('from_ip', 'string', ['length' => 20, 'default' => '', 'comment' => 'ip address']);
        $table->addColumn('created', 'datetime', ['comment' => 'message post time']);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['created']);
        $table->addIndex(['status']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('app_contact');
    }
}
