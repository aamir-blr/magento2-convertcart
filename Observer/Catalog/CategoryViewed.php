<?php
namespace Convertcart\Analytics\Observer\Catalog;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Registry;

class CategoryViewed implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Convertcart\Analytics\Logger\Logger
     */
    protected $_logger;

    /**
     * @var \Convertcart\Analytics\Model\Cc
     */
    protected $_ccModel;

    public function __construct(
        Registry $_registry,
        \Convertcart\Analytics\Logger\Logger $_logger,
        \Convertcart\Analytics\Model\Cc $_ccModel
    ) {
        $this->_registry = $_registry;
        $this->_logger = $_logger;
        $this->_ccModel = $_ccModel;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $eventName = 'categoryViewed';
            $eventData = [];
            $category = $this->_registry->registry('current_category');
            if (is_object($category)) {
                $eventData['name'] = $category->getName();
                $eventData['id'] = $category->getId();
                $eventData['url'] = $category->getUrl();
            }
            $this->_ccModel->storeCcEvents($eventName, $eventData);
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
        }
    }
}
