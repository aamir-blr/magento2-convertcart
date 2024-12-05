<?php


namespace Convertcart\Analytics\Model\ResourceModel\Sync;

use Convertcart\Analytics\Model\Sync;
use Convertcart\Analytics\Model\ResourceModel\Sync as SyncResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(Sync::class, SyncResourceModel::class);
    }
}
