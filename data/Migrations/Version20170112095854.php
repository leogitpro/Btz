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

        $table->addColumn('action', 'string', ['fixed' => true, 'length' => 36]);
        $table->addColumn('dept', 'string', ['fixed' => true, 'length' => 36]);
        $table->addColumn('status', 'smallint', ['default' => 0]);

        $table->setPrimaryKey(['action', 'dept']);

        $table->addIndex(['dept']);
        $table->addIndex(['action']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('sys_acl_department');
    }
}
