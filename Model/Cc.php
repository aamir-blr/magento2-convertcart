<?php
namespace Convertcart\Analytics\Model;

use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\StoreManagerInterface;

class Cc extends \Magento\Framework\Session\SessionManager
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Convertcart\Analytics\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_fwSession;

    public function __construct(
        LayoutInterface $_layout,
        StoreManagerInterface $_storeManager,
        SessionManagerInterface $_fwSession,
        \Convertcart\Analytics\Helper\Data $_dataHelper
    ) {
        $this->_layout = $_layout;
        $this->_storeManager = $_storeManager;
        $this->_fwSession = $_fwSession;
        $this->_dataHelper = $_dataHelper;
    }

    public function getInitScript()
    {
        if ($this->_dataHelper->isEnabled() == false) {
            return;
        }

        $clientKey = $this->_dataHelper->getClientKey();
        if (empty($clientKey)) {
            return;
        }
        $script = $this->_layout->createBlock('Convertcart\Analytics\Block\Script')
                ->setTemplate('Convertcart_Analytics::init.phtml')
                ->assign([
                    'clientKey' => $clientKey
                ]);
        return $script;
    }

    public function getEventScript($eventData = [])
    {
        if ($this->_dataHelper->isEnabled() == false) {
            return;
        }
        $script = $this->_layout->createBlock('Convertcart\Analytics\Block\Script')
        ->setTemplate('Convertcart_Analytics::event.phtml')
        ->assign([
            'eventData' => json_encode($eventData)
        ]);
        return $script;
    }

    public function storeCcEvents($eventName, $eventData = [])
    {
        if ($this->_dataHelper->isEnabled() == false) {
            return;
        }
        $ccEvents = $this->_fwSession->getCcEvents();
        $eventData['ccEvent'] = $this->_dataHelper->getEventType($eventName);
        $eventLimit = 3;
        if (empty($ccEvents)) {
            $ccEvents = [];
            $ccEvents[] = $this->addMetaData($eventData);
        } elseif (count($ccEvents) >= $eventLimit) {
            $eventIndex = $eventLimit - 1;
            $ccEvents[$eventIndex] = $this->addMetaData($eventData);
        } else {
            $ccEvents[] = $this->addMetaData($eventData);
        }

        $this->_fwSession->setCcEvents($ccEvents);
    }

    public function fetchCcEvents()
    {
        if ($this->_dataHelper->isEnabled() == false) {
            return;
        }

        $ccEvents = $this->_fwSession->getCcEvents();
        $this->_fwSession->setCcEvents();
        if (empty($ccEvents)) {
            return [];
        } else {
            return $ccEvents;
        }
    }

    public function addMetaData($eventData = [])
    {
        $metaData = [];
        $metaData['plugin_version'] = $this->_dataHelper->getModuleVersion();
        $eventData['meta_data'] = $metaData;
        return $eventData;
    }

    public function getCartEventData($quote, $currency)
    {
        if (!is_object($quote)) {
            return;
        }
        $cartItems = $quote->getAllVisibleItems();
        $eventData = [];
        $eventData['currency'] = $currency;
        $eventData['items'] = [];
        foreach ($cartItems as $item) {
            $cartItem = [];
            $cartItem['name'] = str_replace("'", "", $item->getName());
            $cartItem['price'] = $item->getPrice();
            $cartItem['currency'] = $currency;
            $cartItem['quantity'] = $item->getQty();
            $cartItem['id'] = $item->getProductId();
            $cartItem['sku'] = $item->getSku();
            $cartItem['customOptions'] = $this->getCartItemOptions($item);

            $product = $item->getProduct();
            if (is_object($product)) {
                $cartItem['url'] = $product->getProductUrl();
                if (!empty($product->getSmallImage())) {
                    $store = $this->_storeManager->getStore();
                    $cartItem['image'] = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                    $cartItem['image'].= 'catalog/product'.$product->getSmallImage();
                }
            }
            $eventData['items'][] = $cartItem;
        }
        $eventData['currency'] = $currency;
        $eventData['coupon_code'] = $quote->getCouponCode();
        $eventData['subtotal'] = $quote->getSubtotal();
        $eventData['total'] = $quote->getGrandTotal();
        $eventData['base_total'] = $quote->getBaseGrandTotal();

        return $eventData;
    }

    public function getCartItemOptions($item)
    {
        if (!is_object($item)) {
            return null;
        }

        $product = $item->getProduct();
        if (!is_object($product)) {
            return null;
        }

        $productInstance = $product->getTypeInstance(true);
        if (!is_object($productInstance)) {
            return null;
        }

        $productOptions = $productInstance->getOrderOptions($product);
        $options = !empty($productOptions['options']) ? $productOptions['options'] : null;
        if (empty($options)) {
            return null;
        }

        $customOptions = [];
        foreach ($options as $option) {
            $customOption = [];
            $customOption['label'] = !empty($option['label']) ? $option['label'] : null;
            $customOption['value'] = !empty($option['value']) ? $option['value'] : null;
            $customOption['option_id'] = !empty($option['option_id']) ? $option['option_id'] : null;
            $customOption['option_type'] = !empty($option['option_type']) ? $option['option_type'] : null;
            $customOptions[] = $customOption;
        }

        return $customOptions;
    }

    public function getCustomerData($customer)
    {
        if (!is_object($customer)) {
            return;
        }
        $customerData = [];
        $customerData['email'] = $customer->getEmail();
        $customerData['first_name'] = $customer->getFirstname();
        $customerData['last_name'] = $customer->getLastname();
        $customerData['id'] = $customer->getId();

        return $customerData;
    }
}
