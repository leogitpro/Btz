<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170222034913 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('sys_feedback');

        $table->addColumn('id', 'string', ['fixed' => true, 'length' => 36]);
        $table->addColumn('sender', 'string', ['fixed' => true, 'length' => 36]);
        $table->addColumn('content', 'text');
        $table->addColumn('created', 'datetime');
        $table->addColumn('replier', 'string', ['fixed' => true, 'length' => 36]);
        $table->addColumn('reply', 'text');
        $table->addColumn('updated', 'datetime');

        $table->setPrimaryKey(['id']);
        $table->addIndex(['sender']);
        $table->addIndex(['replier']);
        $table->addIndex(['created']);
        $table->addIndex(['updated']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('sys_feedback');
    }
}
