<?php

namespace Triggmine\Connector\Observer\Customer;

use Magento\Framework\Event\ObserverInterface;

class CustomerRegisterSuccess implements ObserverInterface
{
    protected $logger;
    protected $customerFactory;
    protected $helper;
    protected $registry;

    /**
     * Customer Register Success constructor.
     *
     * @param \Psr\Log\LoggerInterface $loggerInterface
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Triggmine\Connector\Helper\Data $data
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Psr\Log\LoggerInterface $loggerInterface,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Triggmine\Connector\Helper\Data $data,
        \Magento\Framework\Registry $registry
    )
    {
        $this->logger           = $loggerInterface;
        $this->customerFactory  = $customerFactory;
        $this->helper           = $data;
        $this->registry         = $registry;
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
        
        $customer               = $observer->getEvent()->getCustomer();
        $customerEmail          = $customer->getEmail();
        $customerId             = $customer->getId();
        $customerFirstName      = $customer->getFirstname();
        $customerLastName       = $customer->getLastname();
        $customerDateCreated    = $customer->getCreatedAt();

        try {
            
            $emailReg = $this->registry->registry($customerEmail . 'tm_customer_save');
            
            if ($emailReg) {
                return $this;
            }

            $this->registry->register($customerEmail . 'tm_customer_save', $customerEmail);

            $data = array(
                'device_id'             => $this->helper->getDeviceId(),
                'device_id_1'           => $this->helper->getDeviceId_1(),
                'customer_id'           => $customerId,
                'customer_first_name'   => $customerFirstName,
                'customer_last_name'    => $customerLastName,
                'customer_email'        => $customerEmail,
                'customer_date_created' => $this->helper->setDateFormat($customerDateCreated)
            );
            
            $this->helper->debug(json_encode($data));
            $res = $this->helper->sendRegisterData($data);
            $this->helper->debug(json_encode($res));
            
        } catch (\Exception $e) {
            
            $this->logger->critical($e);
        }

        return $this;
    }
}