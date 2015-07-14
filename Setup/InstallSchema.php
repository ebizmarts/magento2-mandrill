<?php
/**
 * Author: info@ebizmarts.com
 * Date: 7/9/15
 * Time: 9:14 PM
 * File: InstallSchema.php
 * Module: magento2-mandrill
 */

namespace Ebizmarts\Mandrill\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        $table  = $installer->getConnection()
            ->newTable($installer->getTable('mandrill_templates'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Template Id'
            )
            ->addColumn(
                'magento_template_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                128,
                ['nullable' => true, 'default' => null],
                'Magento Template'
            )
            ->addColumn(
                'mandrill_template_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                128,
                ['nullable' => true, 'default' => null],
                'Mandrill Template'
            )
            ->setComment('Magento/Mandrill templates relation');
        $installer->getConnection()->createTable($table);

    }
}