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

        $table->addColumn('dept_id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $table->addColumn('dept_name', 'string', ['length' => 45, 'default' => '']);
        $table->addColumn('dept_status', 'smallint', ['default' => 0]);
        $table->addColumn('dept_created', 'datetime');

        $table->setPrimaryKey(['dept_id']);
        $table->addUniqueIndex(['dept_name'], 'unique_index_dept_name');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('sys_department');
    }
}
