<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170228032218 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('app_wx_qrcode');

        $table->addColumn('id', 'string', ['fixed' => true, 'length' => 36]);
        $table->addColumn('wx', 'integer', ['unsigned' => true]);
        $table->addColumn('name', 'string', ['length' => 45]);
        $table->addColumn('type', 'string', ['fixed' => true, 'length' => 18]);
        $table->addColumn('expired', 'integer', ['unsigned' => true]);
        $table->addColumn('scene', 'string', ['length' => 64]);
        $table->addColumn('url', 'string', ['length' => 255]);
        $table->addColumn('created', 'datetime');

        $table->setPrimaryKey(['id']);
        $table->addIndex(['wx']);
        $table->addIndex(['expired']);
        $table->addIndex(['created']);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('app_wx_qrcode');

    }
}
