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
        $content->addColumn('id', 'string', ['fixed' => true, 'length' => 36]);
        $content->addColumn('status', 'smallint', ['default' => 0]);
        $content->addColumn('topic', 'string', ['length' => 128, 'default' => '']);
        $content->addColumn('content', 'text');
        $content->addColumn('created', 'datetime');

        $content->setPrimaryKey(['id']);

        $content->addIndex(['status']);
        $content->addIndex(['created']);


        $box = $schema->createTable('sys_message_box');
        $box->addColumn('id', 'string', ['fixed' => true, 'length' => 36]);
        $box->addColumn('message_id', 'string', ['fixed' => true, 'length' => 36]);
        $box->addColumn('sender', 'string', ['fixed' => true, 'length' => 36]);
        $box->addColumn('sender_status', 'smallint', ['default' => 0]);
        $box->addColumn('sender_name', 'string', ['length' => 45]);
        $box->addColumn('receiver', 'string', ['fixed' => true, 'length' => 36]);
        $box->addColumn('receiver_status', 'smallint', ['default' => 0]);
        $box->addColumn('receiver_name', 'string', ['length' => 45]);
        $box->addColumn('type', 'smallint', ['default' => 0]);
        $box->addColumn('created', 'datetime');

        $box->setPrimaryKey(['id']);

        $box->addIndex(['message_id']);
        $box->addIndex(['sender', 'sender_status']);
        $box->addIndex(['receiver', 'receiver_status']);
        $box->addIndex(['created']);

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
