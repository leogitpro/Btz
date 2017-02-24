<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170224061249 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('app_wx');

        // Primary key, auto_increment, unsigned
        $table->addColumn('wx_id', 'integer', ['unsigned' => true, 'autoincrement' => true]);

        // foreign key to member, uuid, char(36)
        $table->addColumn('member', 'string', ['fixed' => true, 'length' => 36]);

        $table->addColumn('wx_appid', 'string', ['length' => 45]);
        $table->addColumn('wx_appsecret', 'string', ['length' => 255]);
        $table->addColumn('wx_access_token', 'string', ['length' => 512]);
        $table->addColumn('wx_access_token_expired', 'integer', ['unsigned' => true]);
        $table->addColumn('wx_expired', 'integer', ['unsigned' => true]);
        $table->addColumn('wx_checked', 'smallint');
        $table->addColumn('wx_created', 'datetime');

        $table->setPrimaryKey(['wx_id']);
        $table->addUniqueIndex(['wx_appid']);
        $table->addIndex(['member']);
        $table->addIndex(['wx_created']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('app_wx');
    }
}
