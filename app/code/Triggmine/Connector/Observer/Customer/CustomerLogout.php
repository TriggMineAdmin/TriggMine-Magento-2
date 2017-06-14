<?php

namespace Triggmine\Connector\Observer\Customer;

use Magento\Framework\Event\ObserverInterface;

class CustomerLogout implements ObserverInterface
{
    protected $logger;
    protected $customerFactory;
    protected $helper;

    /**
     * Customer Logout constructor.
     * @param \Psr\Log\LoggerInterface $loggerInterface
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Triggmine\Connector\Helper\Data $data
     */
    public function __construct(
        \Psr\Log\LoggerInterface $loggerInterface,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Triggmine\Connector\Helper\Data $data
    )
    {
        $this->logger           = $loggerInterface;
        $this->customerFactory  = $customerFactory;
        $this->helper           = $data;
    }
    
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isEnabled()) {
            return $this;
        }

        try {
            
            $data = $this->helper->getCustomerLoginData();
            $this->helper->debug(json_encode($data));
            
            $res = $this->helper->sendLogoutData($data);
            $this->helper->debug(json_encode($res));
            
        } catch (\Exception $e) {
            
            $this->logger->critical($e);
        }
    }
}