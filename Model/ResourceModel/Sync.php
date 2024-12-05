<?php


namespace Convertcart\Analytics\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Sync extends AbstractDb
{
    public const MAIN_TABLE = 'convertcart_sync_activity';
    public const ID_FIELD_NAME = 'id';

    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, self::ID_FIELD_NAME);
    }
}
