<?php

namespace Triggmine\Connector\Observer\Page;

use Magento\Framework\Event\ObserverInterface;

class PageInit implements ObserverInterface
{
    protected $logger;
    protected $helper;
    protected $_registry;

    /**
     * Page Init constructor.
     * @param \Psr\Log\LoggerInterface $loggerInterface
     * @param \Triggmine\Connector\Helper\Data $data
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Psr\Log\LoggerInterface $loggerInterface,
        \Triggmine\Connector\Helper\Data $data,
        \Magento\Framework\Registry $registry
    )
    {
        $this->logger           = $loggerInterface;
        $this->helper           = $data;
        $this->_registry        = $registry;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        
        if (!$this->helper->isEnabled() && !$this->helper->isBot()) {
            return $this;
        }
        
        try {
            
            if ($this->_registry->registry('current_product')->getId() && !$this->helper->isBot())
            {
                $product_id = $this->_registry->registry('current_product')->getId();
                
                $data = array(
                  "user_agent"      => $_SERVER['HTTP_USER_AGENT'],
                  "customer"        => $this->helper->getCustomerLoginData(),
                  "products"        => array($this->helper->getProduct($product_id))
                );
            
                $this->helper->debug(json_encode($data));
                
                $res = $this->helper->sendPageInit($data);
                $this->helper->debug(json_encode($res));
            }
            
        } catch (\Exception $e) {
            
            $this->logger->critical($e);
        }
    }
}