<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170224064802 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE `app_wx` AUTO_INCREMENT=9525");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        //todo
    }
}
