<?php
/**
 * Author: info@ebizmarts.com
 * Date: 7/22/15
 * Time: 11:32 PM
 * File: InstallSchema.php
 * Module: magento2-mandrill
 */
namespace Ebizmarts\Mandrill\Setup;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema  implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        $table  = $installer->getConnection()
            ->newTable($installer->getTable('mandrill_mailsent'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Mail Id'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true, 'default' => null],
                'Store Id'
            )
            ->addColumn(
                'mail_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                128,
                ['nullable' => true, 'default' => null],
                'Mail Type'
            )
            ->addColumn(
                'customer_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                128,
                ['nullable'=>true, 'default'=> null],
                'Customer Email'
            )
            ->addColumn(
                'customer_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                128,
                ['nullable'=>true,'default'=>null],
                'Customer Name'
            )
            ->addColumn(
                'coupon_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                128,
                ['nullable'=>true,'default'=>null],
                'Coupon Number'
            )
            ->addColumn(
                'coupon_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable'=>true,'default'=>null],
                'Coupon Type'
            )
            ->addColumn(
                'coupon_amount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable'=>true,'default'=>null],
                'Coupon Amount'
            )
            ->addColumn(
                'sent_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                '12,4',
                ['nullable'=>true,'default'=>null],
                'Coupon Amount'
            )
            ->setComment('Sent mails via Mandrill');
        $installer->getConnection()->createTable($table);

        $table  = $installer->getConnection()
            ->newTable($installer->getTable('mandrill_unsubscribe'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Mail Id'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true, 'default' => null],
                'Store Id'
            )
            ->addColumn(
                'email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                128,
                ['nullable'=>true, 'default'=> null],
                'Customer Email'
            )
            ->addColumn(
                'list',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                128,
                ['nullable'=>true,'default'=>null],
                'Coupon Number'
            )
            ->addColumn(
                'unsubscribed_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                '12,4',
                ['nullable'=>true,'default'=>null],
                'Coupon Amount'
            )
            ->setComment('Unsubsribed Emails from list');
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}