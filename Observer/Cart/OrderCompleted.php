<?php
namespace Convertcart\Analytics\Observer\Cart;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Registry;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Sales\Model\OrderFactory;
use \Psr\Log\LoggerInterface;

class OrderCompleted implements ObserverInterface
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
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_salesOrderFactory;

    protected $_dataHelper;

    public function __construct(
        StoreManagerInterface $storeManager,
        OrderFactory $_salesOrderFactory,
        \Convertcart\Analytics\Logger\Logger $_logger,
        \Convertcart\Analytics\Model\Cc $_ccModel
    ) {
        $this->_storeManager = $storeManager;
        $this->_salesOrderFactory = $_salesOrderFactory;
        $this->_ccModel = $_ccModel;
        $this->_logger = $_logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $orderIds = $observer->getData('order_ids');
            if (!is_array($orderIds) || empty($orderIds[0])) {
                return;
            }

            $eventData = [];
            $eventData['items'] = [];
            $store = $this->_storeManager->getStore();
            $currency = is_object($store) ? $store->getCurrentCurrencyCode() : null;
            $order = $this->_salesOrderFactory->create()->load($orderIds[0]);
            if (!is_object($order)) {
                return;
            }

            foreach ($order->getAllVisibleItems() as $item) {
                $orderItem = [];
                $orderItem['name'] = str_replace("'", "", $item->getName());
                $orderItem['price'] = $item->getPrice();
                $orderItem['currency'] = $currency;
                $orderItem['quantity'] = $item->getQtyOrdered();
                $orderItem['id'] = $item->getProductId();
                $orderItem['sku'] = $item->getSku();

                $product = $item->getProduct();
                if (is_object($product)) {
                    $orderItem['url'] = $product->getProductUrl();
                }
                $eventData['items'][] = $orderItem;
            }

            $eventData['orderId'] = $order->getIncrementId();
            $eventData['order_email'] = $order->getCustomerEmail();
            $eventData['currency'] = $currency;
            $eventData['is_guest'] = $order->getCustomerIsGuest();
            $eventData['coupon_code'] = $order->getCouponCode();
            $eventData['shipping_method'] = $order->getShippingDescription();
            $eventData['payment_method'] = $order->getPayment()->getMethod();
            $eventData['status'] = $order->getStatus();
            $eventData['currency'] = $currency;
            $eventData['total'] = $order->getGrandTotal();

            // $ccData['event_data']['shipping_amount'] = $cc->getValue($order->getShippingAmount());
            // $ccData['event_data']['tax_amount'] = $cc->getValue($order->getTaxAmount());
            // $ccData['event_data']['discount_amount'] = $cc->getValue($order->getDiscountAmount());
            // $ccView['event_data']['subtotal'] = $cc->getValue($order->getSubtotal());

            // $ccData['event_data']['base_total'] = $cc->getValue($order->getBaseGrandTotal());
            // $ccData['event_data']['total_due'] = $cc->getValue($order->getTotalDue());
            // $ccData['event_data']['base_total_due'] = $cc->getValue($order->getBaseTotalDue());

            $eventName = 'orderCompleted';
            $this->_ccModel->storeCcEvents($eventName, $eventData);
        } catch (\Exception $e) {
            $this->_logger->log(null, $e->getMessage());
        }
    }
}
