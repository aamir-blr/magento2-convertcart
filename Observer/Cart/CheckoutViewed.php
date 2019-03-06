<?php
namespace Convertcart\Analytics\Observer\Cart;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Session;

class CheckoutViewed implements ObserverInterface
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
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    public function __construct(
        StoreManagerInterface $_storeManager,
        Session $_checkoutSession,
        \Convertcart\Analytics\Logger\Logger $_logger,
        \Convertcart\Analytics\Model\Cc $_ccModel
    ) {
        $this->_checkoutSession = $_checkoutSession;
        $this->_storeManager = $_storeManager;
        $this->_logger = $_logger;
        $this->_ccModel = $_ccModel;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $eventName  = 'checkoutViewed';
            $eventData = [];
            if (!empty($this->_checkoutSession) && is_object($this->_checkoutSession)) {
                $store = $this->_storeManager->getStore();
                $currency = is_object($store) ? $store->getCurrentCurrencyCode() : null;
                $eventData = $this->_ccModel->getCartEventData($this->_checkoutSession->getQuote(), $currency);
            }
            $this->_ccModel->storeCcEvents($eventName, $eventData);
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
        }
    }
}
