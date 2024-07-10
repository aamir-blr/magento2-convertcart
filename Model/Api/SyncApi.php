<?php

namespace Convertcart\Analytics\Model\Api;

use Convertcart\Analytics\Model\SyncFactory;
use Convertcart\Analytics\Model\ResourceModel\Sync\Collection;

class SyncApi implements \Convertcart\Analytics\Api\SyncRepositoryInterface
{

    /**
     * @var \Convertcart\Analytics\Logger\Logger
     */
    protected $_logger;

    /**
     * @var \Convertcart\Analytics\Model\SyncFactory
     */
    protected $_deletedProduct;

    public function __construct(
        \Convertcart\Analytics\Logger\Logger $_logger,
        \Convertcart\Analytics\Model\SyncFactory $deletedProduct
    ) {
        $this->_logger = $_logger;
        $this->_deletedProduct = $deletedProduct;
    }

    /**
     * Deleted product
     *
     * @inheriDoc
     *
     * @param int $limit
     * @param int $id
     * @param string $type
     */
    public function getDeletedProduct($limit, $id, $type)
    {
        try {
            // to delete the previous synced data ($id is last sync id);
            if ($id) {
                $delete = $this->_deletedProduct->create()
                ->getCollection()
                ->addFieldToFilter("id", ['lteq'=> $id])
                ->addFieldToFilter("type", ["eq"=>$type]);
                $delete->walk('delete');
            }

            $model = $this->_deletedProduct->create()->getCollection();
            $model->getSelect()->where('type = ?', $type)->limit($limit);
            return $model->getData();
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
        }
    }
}
