<?php

namespace Triggmine\Connector\Observer\Export;

use Magento\Framework\Event\ObserverInterface;


class ExportCustomer implements ObserverInterface
{
    protected $logger;
    protected $helper;
    protected $customer;

    /**
     * Export Customer constructor.
     * @param \Psr\Log\LoggerInterface $loggerInterface
     * @param \Triggmine\Connector\Helper\Data $data
     * @param \Magento\Sales\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     */
    public function __construct(
        \Psr\Log\LoggerInterface $loggerInterface,
        \Triggmine\Connector\Helper\Data $data,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
    )
    {
        $this->logger           = $loggerInterface;
        $this->helper           = $data;
        $this->customer         = $customerCollectionFactory;
    }
    
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        if (!$this->helper->isEnabledCustomerExport()) {
            return $this;
        }
        
        try {

            $_fromDate  = $this->helper->getCustomerDateFrom();
            $_toDate    = $this->helper->getCustomerDateTo();
            $_storeId   = $this->helper->getStoreId();
            
            $customers  = $this->customer->create()
                ->addFieldToSelect('*')
                ->addFieldToFilter('store_id', $_storeId)
                ->addAttributeToFilter('created_at', array('from' => $_fromDate, 'to' => $_toDate));
            
            $dataExport = array(
                    'prospects'    => array()
                );
            
            foreach ($customers as $customer) {
                
                $data = array(
                    'customer_id'              => $customer->getId(),
                    'customer_first_name'      => $customer->getFirstname(),
                    'customer_last_name'       => $customer->getLastname(),
                    'customer_email'           => $customer->getEmail(),
                    'customer_date_created'    => $this->helper->setDateFormat($customer->getCreatedAt()),
                    'customer_last_login_date' => $this->helper->setDateFormat($customer->getUpdatedAt())
                );

                $dataExport['prospects'][] = $data;
            }
    
            $this->helper->debug(json_encode($dataExport));

            $res = $this->helper->exportCustomerHistory($dataExport);
            $this->helper->debug(json_encode($res));
            
        } catch (\Exception $e) {
            
            $this->logger->critical($e);
        }
    }
}