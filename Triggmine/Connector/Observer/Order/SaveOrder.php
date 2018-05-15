<?php

namespace Triggmine\Connector\Observer\Order;

use Magento\Framework\Event\ObserverInterface;

class SaveOrder implements ObserverInterface
{
    protected $helper;
    protected $logger;

    /**
     * Save Order constructor.
     * @param \Psr\Log\LoggerInterface $loggerInterface
     * @param \Triggmine\Connector\Helper\Data $data
     */
    public function __construct(
        \Psr\Log\LoggerInterface $loggerInterface,
        \Triggmine\Connector\Helper\Data $data
    )
    {
        $this->logger   = $loggerInterface;
        $this->helper   = $data;
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
            
            $order          = $observer->getEvent()->getOrder();
            $order_id       = $order->getId();
            $order_items    = $order->getAllVisibleItems();

            $data = array(
                'customer'    => $this->helper->getCustomerLoginData(),
                'order_id'    => $order_id,
                'status'      => $order->getStatus() ? $order->getStatus() : 'pending',
                'price_total' => sprintf('%01.2f', $order->getGrandTotal()),
                'qty_total'   => $order->getTotalItemCount(),
                'products'    => array()
            );
            
            foreach($order_items as $item)
            {
                if ($item->getProductType() == "simple")
                {
                    $item_id    = (integer) $item->getProductId();
                    $item_qty   = (integer) $item->getQty();
                    
                    $data['products'][] = $this->helper->getProduct($item_id, $item_qty);
                }
            }
            
            $this->helper->debug(json_encode($data));
            
            $res = $this->helper->onConvertCartToOrder($data);
            $this->helper->debug(json_encode($res));
            
        } catch (\Exception $e) {
            
            $this->logger->critical($e);
        }

        return $this;
    }    
}