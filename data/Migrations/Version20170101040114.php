<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170101040114 extends AbstractMigration
{

    public function getDescription()
    {
        return 'Create table for department with member relation';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('sys_department_member');

        $table->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => ture]);
        $table->addColumn('dept_id', 'integer', ['unsigned' => true, 'default' => 0]);
        $table->addColumn('member_id', 'integer', ['unsigned' => true, 'default' => 0]);
        $table->addColumn('status', 'smallint', ['default' => 0]);
        $table->addColumn('created', 'datetime');

        $table->setPrimaryKey(['id']);
        $table->addIndex(['dept_id', 'member_id'], 'index_dept_member');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('sys_department_member');
    }
}
