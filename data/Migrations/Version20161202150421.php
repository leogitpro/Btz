<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161202150421 extends AbstractMigration
{

    public function getDescription()
    {
        $desc = 'Init administrator account';
        return $desc;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql(
            'INSERT INTO `sys_adminer` (`admin_email`, `admin_passwd`, `admin_name`, `admin_status`, `admin_created`) VALUES (?, ?, ?, ?, ?)',
            ['admin@example.com', strtolower(md5('12345')), 'Administrator', 1, date('Y-m-d H:i:s')]
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DELETE FROM `sys_adminer` WHERE `admin_email` = ?', ['admin@example.com']);
    }
}
