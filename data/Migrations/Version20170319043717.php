<?php

namespace Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170319043717 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $account = $schema->createTable('wechat_account');
        $account->addColumn('wx_id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $account->addColumn('member', 'string', ['fixed' => true, 'length' => 36]);
        $account->addColumn('wx_appid', 'string', ['length' => 45]);
        $account->addColumn('wx_appsecret', 'string', ['length' => 255]);
        $account->addColumn('wx_access_token', 'string', ['length' => 512]);
        $account->addColumn('wx_access_token_expired', 'integer', ['unsigned' => true]);
        $account->addColumn('wx_jsapi_ticket', 'string', ['length' => 512]);
        $account->addColumn('wx_jsapi_ticket_expired', 'integer', ['unsigned' => true]);
        $account->addColumn('wx_card_ticket', 'string', ['length' => 512]);
        $account->addColumn('wx_card_ticket_expired', 'integer', ['unsigned' => true]);
        $account->addColumn('wx_expired', 'integer', ['unsigned' => true]);
        $account->addColumn('wx_checked', 'smallint');
        $account->addColumn('wx_created', 'datetime');
        $account->setPrimaryKey(['wx_id']);
        $account->addUniqueIndex(['wx_appid']);
        $account->addIndex(['member']);
        $account->addIndex(['wx_expired']);
        $account->addIndex(['wx_created']);


        $client = $schema->createTable('wechat_client');
        $client->addColumn('id', 'string', ['fixed' => true, 'length' => 36]);
        $client->addColumn('wx', 'integer', ['unsigned' => true]);
        $client->addColumn('name', 'string', ['length' => 45]);
        $client->addColumn('active_time', 'integer', ['unsigned' => true]);
        $client->addColumn('expire_time', 'integer', ['unsigned' => true]);
        $client->addColumn('domain', 'string', ['length' => 255]);
        $client->addColumn('ip', 'string', ['length' => 255]);
        $client->addColumn('created', 'datetime');
        $client->setPrimaryKey(['id']);
        $client->addIndex(['wx']);
        $client->addIndex(['expire_time']);


        $qrcode = $schema->createTable('wechat_qrcode');
        $qrcode->addColumn('id', 'string', ['fixed' => true, 'length' => 36]);
        $qrcode->addColumn('wx', 'integer', ['unsigned' => true]);
        $qrcode->addColumn('name', 'string', ['length' => 45]);
        $qrcode->addColumn('type', 'string', ['fixed' => true, 'length' => 18]);
        $qrcode->addColumn('expired', 'integer', ['unsigned' => true]);
        $qrcode->addColumn('scene', 'string', ['length' => 64]);
        $qrcode->addColumn('url', 'string', ['length' => 255]);
        $qrcode->addColumn('created', 'datetime');
        $qrcode->setPrimaryKey(['id']);
        $qrcode->addIndex(['wx']);
        $qrcode->addIndex(['expired']);
        $qrcode->addIndex(['created']);


        $menu = $schema->createTable('wechat_menu');
        $menu->addColumn('id', 'string', ['fixed' => true, 'length' => 36]);
        $menu->addColumn('wx', 'integer', ['unsigned' => true]);
        $menu->addColumn('name', 'string', ['length' => 45]);
        $menu->addColumn('menuid', 'string', ['length' => 45]);
        $menu->addColumn('menu', 'text');
        $menu->addColumn('type', 'smallint', ['unsigned' => true]);
        $menu->addColumn('status', 'smallint', ['unsigned' => true]);
        $menu->addColumn('updated', 'datetime');
        $menu->setPrimaryKey(['id']);
        $menu->addIndex(['wx']);
        $menu->addIndex(['status', 'type', 'updated']);


        $tag = $schema->createTable('wechat_tag');
        $tag->addColumn('id', 'string', ['fixed' => true, 'length' => 36]);
        $tag->addColumn('wx', 'integer', ['unsigned' => true]);
        $tag->addColumn('tagid', 'integer', ['unsigned' => true]);
        $tag->addColumn('tagname', 'string', ['length' => 45]);
        $tag->addColumn('tagcount', 'integer', ['unsigned' => true]);
        $tag->setPrimaryKey(['id']);
        $tag->addIndex(['wx']);
        $tag->addIndex(['tagid']);


        $order = $schema->createTable('wechat_order');
        $order->addColumn('id', 'string', ['fixed' => true, 'length' => 36]);
        $order->addColumn('wx', 'integer', ['unsigned' => true]);
        $order->addColumn('no', 'string', ['fixed' => true, 'length' => 14]);
        $order->addColumn('money', 'integer', ['unsigned' => true]);
        $order->addColumn('paid', 'smallint', ['unsigned' => true]);
        $order->addColumn('second', 'integer', ['unsigned' => true]);
        $order->addColumn('created', 'datetime');
        $order->setPrimaryKey(['id']);
        $order->addIndex(['wx']);
        $order->addIndex(['wx', 'no']);

        $invoice = $schema->createTable('wechat_invoice');
        $invoice->addColumn('id', 'string', ['fixed' => true, 'length' => 36]);
        $invoice->addColumn('wx', 'integer', ['unsigned' => true]);
        $invoice->addColumn('title', 'string', ['length' => 100]);
        $invoice->addColumn('receiver', 'string', ['length' => 45]);
        $invoice->addColumn('phone', 'string', ['length' => 45]);
        $invoice->addColumn('address', 'string', ['length' => 100]);
        $invoice->addColumn('money', 'integer', ['unsigned' => true]);
        $invoice->addColumn('status', 'smallint', ['unsigned' => true]);
        $invoice->addColumn('note', 'string', ['length' => 255]);
        $invoice->addColumn('created', 'datetime');
        $invoice->setPrimaryKey(['id']);
        $invoice->addIndex(['wx']);

        $oauth = $schema->createTable('wechat_oauth');
        $oauth->addColumn('id', 'string', ['fixed' => true, 'length' => 32]);
        $oauth->addColumn('url', 'string', ['length' => 255]);
        $oauth->addColumn('created', 'datetime');
        $oauth->setPrimaryKey(['id']);

    }

    public function postUp(Schema $schema)
    {
        parent::postUp($schema);

        $this->connection->exec("ALTER TABLE `wechat_account` AUTO_INCREMENT=9526");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('wechat_account');
        $schema->dropTable('wechat_client');
        $schema->dropTable('wechat_qrcode');
        $schema->dropTable('wechat_menu');
        $schema->dropTable('wechat_tag');
        $schema->dropTable('wechat_order');
        $schema->dropTable('wechat_invoice');

    }
}
