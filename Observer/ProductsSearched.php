<?php
namespace Convertcart\Analytics\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\Request\Http;

class ProductsSearched implements ObserverInterface
{
    /**
     * @var \Convertcart\Analytics\Logger\Logger
     */
    protected $_logger;

    /**
     * @var \Convertcart\Analytics\Model\Cc
     */
    protected $_ccModel;

    /**
     * @var \Convertcart\Analytics\Helper\Data
     */
    protected $_dataHelper;

    public function __construct(
        \Convertcart\Analytics\Logger\Logger $_logger,
        \Convertcart\Analytics\Model\Cc $_ccModel,
        \Convertcart\Analytics\Helper\Data $_dataHelper
    ) {
        $this->_logger = $_logger;
        $this->_ccModel = $_ccModel;
        $this->_dataHelper = $_dataHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $eventName = 'productsSearched';
            $eventData = [];
            $query = $observer->getDataObject();
            if (is_object($query)) {
                $eventData['query'] = $this->_dataHelper->sanitizeParam($query->getQueryText());
            }
            $this->_ccModel->storeCcEvents($eventName, $eventData);
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
        }
    }
}