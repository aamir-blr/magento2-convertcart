<?php
namespace Convertcart\Analytics\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ResourceConnection;
use Convertcart\Analytics\Model\SyncFactory;

class ProcessProductAfterDeleteEventObserver implements ObserverInterface
{

    /**
     * @var \Convertcart\Analytics\Model\SyncFactory
     */
    protected $_deletedProduct;

    /**
     * @var \Convertcart\Analytics\Logger\Logger
     */
    protected $_logger;

    public function __construct(
        \Convertcart\Analytics\Model\SyncFactory $deletedProduct,
        \Convertcart\Analytics\Logger\Logger $_logger
    ) {
        $this->_logger = $_logger;
        $this->_deletedProduct = $deletedProduct;
    }

    public function execute(Observer $observer)
    {
        try {
            $eventProduct = $observer->getEvent()->getProduct();
            $model = $this->_deletedProduct->create();
            $model->addData(["item_id" => $eventProduct->getId()]);
            $model->addData(["type" => "product"]);
            $saveData = $model->save();
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
        }
    }
}
