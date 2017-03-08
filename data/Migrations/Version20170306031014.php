<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170306031014 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('app_wx_menu');

        $table->addColumn('id', 'string', ['fixed' => true, 'length' => 36]);
        $table->addColumn('wx', 'integer', ['unsigned' => true]);
        $table->addColumn('name', 'string', ['length' => 45]);
        $table->addColumn('menu', 'text');
        $table->addColumn('type', 'smallint', ['unsigned' => true]);
        $table->addColumn('updated', 'datetime');

        $table->setPrimaryKey(['id']);
        $table->addIndex(['wx']);
        $table->addIndex(['type']);
        $table->addIndex(['updated']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('app_wx_menu');
    }
}
