<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170112095854 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('sys_acl_department');

        $table->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $table->addColumn('action_id', 'integer', ['unsigned' => true, 'default' => 0]);
        $table->addColumn('dept_id', 'integer', ['unsigned' => true, 'default' => 0]);
        $table->addColumn('status', 'smallint', ['default' => 0]);
        $table->addColumn('created', 'datetime');

        $table->setPrimaryKey(['id']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('sys_acl_department');
    }
}
