<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170119142408 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $content = $schema->createTable('sys_message_content');
        $content->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $content->addColumn('status', 'smallint', ['default' => 0]);
        $content->addColumn('topic', 'string', ['length' => 128, 'default' => '']);
        $content->addColumn('content', 'text');
        $content->addColumn('created', 'datetime');

        $content->setPrimaryKey(['id']);

        $inbox = $schema->createTable('sys_message_box');
        $inbox->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $inbox->addColumn('message_id', 'integer', ['unsigned' => true, 'default' => 0]);
        $inbox->addColumn('sender', 'integer', ['unsigned' => true, 'default' => 0]);
        $inbox->addColumn('sender_status', 'smallint', ['default' => 0]);
        $inbox->addColumn('receiver', 'integer', ['unsigned' => true, 'default' => 0]);
        $inbox->addColumn('receiver_status', 'smallint', ['default' => 0]);
        $inbox->addColumn('type', 'smallint', ['default' => 0]);
        $inbox->addColumn('created', 'datetime');

        $inbox->setPrimaryKey(['id']);

        $inbox->addIndex(['sender', 'sender_status']);
        $inbox->addIndex(['receiver', 'receiver_status']);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('sys_message_content');
        $schema->dropTable('sys_message_box');
    }
}
