<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170101035232 extends AbstractMigration
{

    public function getDescription()
    {
        return 'Create department table';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('sys_department');

        $table->addColumn('dept_id', 'string', ['fixed' => true, 'length' => 36]);
        $table->addColumn('dept_name', 'string', ['length' => 45, 'default' => '']);
        $table->addColumn('dept_status', 'smallint', ['default' => 0]);
        $table->addColumn('dept_created', 'datetime');

        $table->setPrimaryKey(['dept_id']);

        // Query for dept by name
        $table->addUniqueIndex(['dept_name']);

        // Query for activated dept
        $table->addIndex(['dept_status']);

        // Query for default order
        $table->addIndex(['dept_status', 'dept_name']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('sys_department');
    }
}
