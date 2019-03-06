<?php
namespace Convertcart\Analytics\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\LayoutInterface;

class AddScript implements ObserverInterface
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

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
        LayoutInterface $_layout,
        \Convertcart\Analytics\Logger\Logger $_logger,
        \Convertcart\Analytics\Helper\Data $_dataHelper,
        \Convertcart\Analytics\Model\Cc $_ccModel
    ) {
        $this->_dataHelper = $_dataHelper;
        $this->_logger = $_logger;
        $this->_ccModel = $_ccModel;
        $this->_layout = $_layout;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $initScript = $this->_ccModel->getInitScript();
            if (empty($initScript)) {
                return;
            }

            $layout = $this->_layout;
            if (!is_object($layout)) {
                return;
            }

            $head = $layout->getBlock('head.additional');
            if (!is_object($head)) {
                return;
            }
            $head->append($initScript);
            $this->attachEvents($head);
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
        }
    }

    private function attachEvents($head)
    {
        if (!is_object($head)) {
            return;
        }
        $ccEvents = $this->_ccModel->fetchCcEvents();
        if (empty($ccEvents) or !is_array($ccEvents)) {
            return;
        }
        foreach ($ccEvents as $ccEvent) {
            $eventBlock = $this->_ccModel->getEventScript($ccEvent);
            $head->append($eventBlock);
        }
    }
}



