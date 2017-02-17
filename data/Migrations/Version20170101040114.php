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

        $table->addColumn('dept', 'string', ['fixed' => true, 'length' => 36]);
        $table->addColumn('member', 'string', ['fixed' => true, 'length' => 36]);

        $table->setPrimaryKey(['dept', 'member']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('sys_department_member');
    }
}
