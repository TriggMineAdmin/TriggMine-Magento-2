<?php

namespace Triggmine\Connector\Observer\Export;

use Magento\Framework\Event\ObserverInterface;


class ExportOrderHistory implements ObserverInterface
{
    protected $logger;
    protected $helper;
    protected $orders;

    /**
     * Export Order History constructor.
     * @param \Psr\Log\LoggerInterface $loggerInterface
     * @param \Triggmine\Connector\Helper\Data $data
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollect
     */
    public function __construct(
        \Psr\Log\LoggerInterface $loggerInterface,
        \Triggmine\Connector\Helper\Data $data,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    )
    {
        $this->logger           = $loggerInterface;
        $this->helper           = $data;
        $this->orders           = $orderCollectionFactory;
    }
    
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        if (!$this->helper->isEnabledOrderExport()) {
            return $this;
        }
        
        try {

            $_fromDate  = $this->helper->getExportDateFrom();
            $_toDate    = $this->helper->getExportDateTo();
            $_storeId   = $this->helper->getStoreId();
            
            $orders     = $this->orders->create()
                ->addFieldToSelect('*')
                ->addFieldToFilter('store_id', $_storeId)
                ->addAttributeToFilter('created_at', array('from' => $_fromDate, 'to' => $_toDate));
            
            $dataExport = array(
                    'orders'    => array()
                );
            
            foreach ($orders as $order) {
                
                $data = array(
                    'customer'      => $this->helper->getCustomerData($order->getCustomerId()),
                    'order_id'      => $order->getId(),
                    'date_created'  => $order->getCreatedAt(),
                    'status'        => $order->getStatus() ? $order->getStatus() : 'pending',
                    'price_total'   => sprintf('%01.2f', $order->getGrandTotal()),
                    'qty_total'     => $order->getTotalItemCount(),
                    'products'      => array()
                );
                
                foreach($order->getAllVisibleItems() as $item)
                {
                    $item_id    = (integer) $item->getProductId();
                    $item_qty   = (integer) $item->getQtyOrdered();
                        
                    $data['products'][] = $this->helper->getProduct($item_id, $item_qty);
                }
                
                $dataExport['orders'][] = $data;
            }
    
            $this->helper->debug(json_encode($dataExport));

            $res = $this->helper->exportOrderHistory($dataExport);
            $this->helper->debug(json_encode($res));
            
        } catch (\Exception $e) {
            
            $this->logger->critical($e);
        }
    }
}