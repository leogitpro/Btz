<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161228093114 extends AbstractMigration
{

    private $tableName = 'contact';

    public function getDescription()
    {
        return 'Create contact table';
    }


    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable($this->tableName);
        $table->addColumn('contact_id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $table->addColumn('contact_email', 'string', ['length' => 45, 'default' => '', 'comment' => '用户邮件地址']);
        $table->addColumn('contact_subject', 'string', ['length' => 128, 'default' => '', 'comment' => 'Subject']);
        $table->addColumn('contact_content', 'text', ['default' => '', 'notnull' => false, 'comment' => 'Message content']);
        $table->addColumn('contact_is_read', 'smallint', ['unsigned' => true, 'default' => 0, 'comment' => 'message read status']);
        $table->addColumn('contact_status', 'smallint', ['unsigned' => true, 'default' => 0, 'comment' => 'message delete status']);
        $table->addColumn('contact_from_ip', 'string', ['length' => 20, 'default' => '', 'comment' => 'ip address']);
        $table->addColumn('contact_created', 'datetime', ['comment' => 'message post time']);
        $table->setPrimaryKey(['contact_id']);
    }


    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable($this->tableName);
    }
}
