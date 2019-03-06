<?php
namespace Convertcart\Analytics\Observer\Catalog;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Registry;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\ConfigurableProduct\Model\Product\Type\ConfigurableFactory;

class ProductViewed implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_stockItemRepository;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\ConfigurableFactory
     */
    protected $_configurableProductProductTypeConfigurableFactory;

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
        StockRegistryInterface $_stockItemRepository,
        StoreManagerInterface $_storeManager,
        ConfigurableFactory $_configurableProductProductTypeConfigurableFactory,
        \Convertcart\Analytics\Logger\Logger $_logger,
        \Convertcart\Analytics\Model\Cc $_ccModel
    ) {
        $this->_registry = $_registry;
        $this->_stockItemRepository = $_stockItemRepository;
        $this->_storeManager = $_storeManager;
        $this->_configurableProductProductTypeConfigurableFactory = $_configurableProductProductTypeConfigurableFactory;
        $this->_logger = $_logger;
        $this->_ccModel = $_ccModel;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $eventName = 'productViewed';
            $eventData = [];
            $product = $this->_registry->registry('current_product');
            if (is_object($product)) {
                $eventData = [
                    'id' => $product->getId(),
                    'url' => $product->getProductUrl(),
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
                    'final_price' => $product->getFinalPrice(),
                    'sku' => $product->getSku(),
                    'type' => $product->getTypeId()
                ];

                $store = $this->_storeManager->getStore();
                $eventData['currency'] = is_object($store) ? $store->getCurrentCurrencyCode() : null;
                if (!empty($product->getImage())) {
                    $eventData['image'] = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                    $eventData['image'].= 'catalog/product'.$product->getImage();
                } else {
                    $eventData['image'] = null;
                }
                $stock = $this->_stockItemRepository->getStockItem($product->getId());
                $eventData['is_in_stock'] = is_object($stock) ? $stock->getIsInStock() : null;
                if ($eventData['type'] == "configurable") {
                    $eventData['product_type'] = "parent";
                    $childProducts = $this->_configurableProductProductTypeConfigurableFactory->create()
                                        ->getChildrenIds($product->getId());
                    $eventData['child_ids'] = $childProducts[0];
                } else {
                    $eventData['product_type'] = "simple";
                }
            }
            $this->_ccModel->storeCcEvents($eventName, $eventData);
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
        }
    }
}
