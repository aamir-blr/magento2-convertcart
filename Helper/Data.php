<?php
namespace Convertcart\Analytics\Helper;

use Magento\Framework\Module\ModuleListInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $_moduleList;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        ModuleListInterface $moduleList
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->_moduleList = $moduleList;
        parent::__construct(
            $context
        );
    }

    public function getEventType($event)
    {
        $eventMap = [
            'homepageViewed'      =>  'homepageViewed',
            'contentPageViewed'   =>  'contentPageViewed',
            'categoryViewed'      =>  'categoryViewed',
            'productViewed'       =>  'productViewed',
            'productsSearched'    =>  'productsSearched',
            'registered'          =>  'registered',
            'loggedIn'            =>  'loggedIn',
            'loggedOut'           =>  'loggedOut',
            'cartViewed'          =>  'cartViewed',
            'checkoutViewed'      =>  'checkoutViewed',
            'cartUpdated'         =>  'cartUpdated',
            'productAdded'        =>  'productAdded',
            'productRemoved'      =>  'productRemoved',
            'orderCompleted'      =>  'orderCompleted',
            'couponApplied'       =>  'couponApplied',
            'couponDenied'        =>  'couponDenied',
            'couponRemoved'       =>  'couponRemoved',
        ];
        if (!empty($eventMap[$event])) {
            return $eventMap[$event];
        } else {
            return 'default';
        }
    }

    public function isEnabled()
    {
        if ($this->getClientKey()) {
            return 1;
        } else {
            return false;
        }
    }

    public function getClientKey()
    {
        $clientKey = $this->scopeConfig->getValue('convertcart/configuration/domainid', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (empty($clientKey)) {
            return false;
        } else {
            return $clientKey;
        }
    }

    public function getModuleVersion()
    {
        $ccModule = $this->_moduleList
            ->getOne('Convertcart_Analytics');
        return !empty($ccModule['setup_version']) ? $ccModule['setup_version'] : null;
    }

    public function sanitizeParam($param)
    {
        if ($param === null) {
            return null;
        }
        return strip_tags($param);
    }
}