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

        $table->addColumn('controller_class', 'string', ['length' => 100, 'default' => '']);
        $table->addColumn('controller_name', 'string', ['length' => 100, 'default' => '']);
        $table->addColumn('controller_icon', 'string', ['length' => 45, 'default' => '']);
        $table->addColumn('controller_route', 'string', ['length' => 45, 'default' => '']);
        $table->addColumn('controller_rank', 'integer', ['unsigned' => true, 'default' => 0]);
        $table->addColumn('controller_menu', 'smallint', ['unsigned' => true, 'default' => 0]);

        $table->setPrimaryKey(['controller_class']);

        // Index for: generate menu,
        $table->addIndex(['controller_rank', 'controller_name']);

        $table->addIndex(['controller_rank', 'controller_menu', 'controller_name']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('sys_controller');

    }
}
