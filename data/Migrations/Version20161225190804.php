<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161225190804 extends AbstractMigration
{

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Add index for active token and password reset token';
    }


    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->getTable('user');
        $table->addIndex(['active_token'], 'index_active_token');
        $table->addIndex(['pwd_reset_token'], 'index_pwd_reset_token');
    }


    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $table = $schema->getTable('user');
        $table->dropIndex('index_active_token');
        $table->dropIndex('index_pwd_reset_token');
    }
}
