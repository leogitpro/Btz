<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170314031848 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('app_wx_tag');

        $table->addColumn('id', 'string', ['fixed' => true, 'length' => 36]);
        $table->addColumn('wx', 'integer', ['unsigned' => true]);
        $table->addColumn('tagid', 'integer', ['unsigned' => true]);
        $table->addColumn('tagname', 'string', ['length' => 45]);
        $table->addColumn('tagcount', 'integer', ['unsigned' => true]);

        $table->setPrimaryKey(['id']);
        $table->addIndex(['wx']);
        $table->addIndex(['tagid']);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('app_wx_tag');

    }
}
