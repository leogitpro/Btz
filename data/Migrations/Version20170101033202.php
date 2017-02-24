<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170101033202 extends AbstractMigration
{

    public function getDescription()
    {
        return 'Create system administrator table';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        $table = $schema->createTable('sys_member'); // Table name: sys_member

        // Setting a global in connection configuration. use configuration mode.
        /**
        $table->addOption('engine', 'InnoDB');
        $table->addOption('charset', 'utf8mb4');
        $table->addOption('collate', 'utf8mb4_unicode_ci');
        //*/

        $table->addColumn('member_id', 'string', ['fixed' => true, 'length' => 36]);
        $table->addColumn('member_email', 'string', ['length' => 45, 'default' => '']);
        $table->addColumn('member_password', 'string', ['length' => 32, 'fixed' => true, 'default' => '']);
        $table->addColumn('member_name', 'string', ['length' => 45, 'default' => '']);
        $table->addColumn('member_status', 'smallint', ['default' => 0, 'comment' => 'Account status']);
        $table->addColumn('member_level', 'smallint', ['default' => 0, 'comment' => 'Account level']);
        $table->addColumn('member_expired', 'datetime');
        $table->addColumn('member_created', 'datetime');

        // Set table primary key.
        $table->setPrimaryKey(['member_id']);

        // Add Unique index
        $table->addUniqueIndex(['member_email']);

        // Index for default query order
        $table->addIndex(['member_status', 'member_level', 'member_name']);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('sys_member');
    }
}
