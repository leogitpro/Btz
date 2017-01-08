<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170107091458 extends AbstractMigration
{

    public function getDescription()
    {
        return 'System actions';
    }


    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('sys_action');

        $table->addColumn('action_id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $table->addColumn('controller_class', 'string', ['length' => 100, 'default' => '']);
        $table->addColumn('action_key', 'string', ['length' => 45, 'default' => '']);
        $table->addColumn('action_name', 'string', ['length' => 45, 'default' => '']);
        $table->addColumn('action_icon', 'string', ['length' => 45, 'default' => '']);
        $table->addColumn('action_rank', 'integer', ['unsigned' => true, 'default' => 0]);
        $table->addColumn('action_menu', 'smallint', ['unsigned' => true, 'default' => 0]);
        $table->addColumn('action_status', 'smallint', ['unsigned' => true, 'default' => 0]);
        $table->addColumn('action_created', 'datetime');

        $table->setPrimaryKey(['action_id']);
        $table->addIndex(['action_rank']);
        $table->addIndex(['controller_class']);
        $table->addIndex(['controller_class', 'action_menu', 'action_status']);
        $table->addIndex(['controller_class', 'action_key', 'action_status']);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('sys_action');
    }
}
