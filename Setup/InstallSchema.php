<?php

namespace Convertcart\Analytics\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * Create table
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $conn = $setup->getConnection();
        $tableName = $setup->getTable('convertcart_sync_activity');
        if ($conn->isTableExists($tableName) != true) {

            $table = $conn->newTable($tableName)
                        ->addColumn(
                            'id',
                            Table::TYPE_INTEGER,
                            null,
                            ['unsigned'=>true,'nullable'=>false,'auto_increment' => true,'primary'=>true]
                        )
                        ->addColumn(
                            'item_id',
                            Table::TYPE_INTEGER,
                            null,
                            ['nullable'=>false]
                        )
                        ->addColumn(
                            'type',
                            Table::TYPE_TEXT,
                            55,
                            ['nullable'=>false]
                        )
                        ->addColumn(
                            'created_at',
                            Table::TYPE_TIMESTAMP,
                            null,
                            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT]
                        )
                        ->setOption('charset', 'utf8');
            $conn->createTable($table);
        }
        $setup->endSetup();
    }
}
