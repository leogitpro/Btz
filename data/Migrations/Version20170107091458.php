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

        $table->addColumn('action_id', 'string', ['fixed' => true, 'length' => 36]);
        $table->addColumn('controller_key', 'string', ['length' => 100, 'default' => '']);
        $table->addColumn('action_key', 'string', ['length' => 45, 'default' => '']);
        $table->addColumn('action_name', 'string', ['length' => 45, 'default' => '']);
        $table->addColumn('action_icon', 'string', ['length' => 45, 'default' => '']);
        $table->addColumn('action_rank', 'integer', ['unsigned' => true, 'default' => 0]);
        $table->addColumn('action_menu', 'smallint', ['unsigned' => true, 'default' => 0]);

        $table->setPrimaryKey(['action_id']);

        // Index for: generate menu,
        $table->addIndex(['action_menu']);
        $table->addIndex(['action_rank', 'action_name']);


        $table->addIndex(['action_rank']);
        $table->addIndex(['controller_key']);
        $table->addIndex(['action_rank', 'action_menu', 'action_name']);
        $table->addIndex(['controller_key', 'action_menu']);
        $table->addIndex(['controller_key', 'action_key']);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('sys_action');
    }
}
