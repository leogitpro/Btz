<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170107090339 extends AbstractMigration
{

    public function getDescription()
    {
        return 'System controllers';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('sys_controller');

        $table->addColumn('controller_id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $table->addColumn('controller_class', 'string', ['length' => 100, 'default' => '']);
        $table->addColumn('controller_name', 'string', ['length' => 100, 'default' => '']);
        $table->addColumn('controller_icon', 'string', ['length' => 45, 'default' => '']);
        $table->addColumn('controller_route', 'string', ['length' => 45, 'default' => '']);
        $table->addColumn('controller_rank', 'integer', ['unsigned' => true, 'default' => 0]);
        $table->addColumn('controller_menu', 'smallint', ['unsigned' => true, 'default' => 0]);
        $table->addColumn('controller_status', 'smallint', ['unsigned' => true, 'default' => 0]);
        $table->addColumn('controller_created', 'datetime');

        $table->setPrimaryKey(['controller_id']);
        $table->addUniqueIndex(['controller_class']);
        $table->addIndex(['controller_rank']);
        $table->addIndex(['controller_status']);
        $table->addIndex(['controller_class', 'controller_status']);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('sys_controller');

    }
}
