<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170225071829 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('app_wx_client');

        $table->addColumn('id', 'string', ['fixed' => true, 'length' => 36]);
        $table->addColumn('wx', 'integer', ['unsigned' => true]);
        $table->addColumn('name', 'string', ['length' => 45]);
        $table->addColumn('active_time', 'integer', ['unsigned' => true]);
        $table->addColumn('expire_time', 'integer', ['unsigned' => true]);
        $table->addColumn('domain', 'string', ['length' => 255]);
        $table->addColumn('ip', 'string', ['length' => 255]);
        $table->addColumn('created', 'datetime');

        $table->setPrimaryKey(['id']);
        $table->addIndex(['wx']);
        $table->addIndex(['expire_time']);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('app_wx_client');

    }
}
