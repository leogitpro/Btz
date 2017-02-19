<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170112090228 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('sys_acl_member');

        $table->addColumn('action', 'string', ['fixed' => true, 'length' => 36]);
        $table->addColumn('member', 'string', ['fixed' => true, 'length' => 36]);
        $table->addColumn('status', 'smallint', ['default' => 0]);

        $table->setPrimaryKey(['action', 'member']);

        $table->addIndex(['member']);
        $table->addIndex(['action']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('sys_acl_member');
    }
}
