<?php

namespace Triggmine\Connector\Observer\Cart;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CheckoutCartSaveAfter implements ObserverInterface
{
    protected $helper;
    protected $logger;
    
    /**
     * CheckoutCartSaveAfter constructor.
     * @param \Psr\Log\LoggerInterface $loggerInterface
     * @param \Triggmine\Connector\Helper\Data $data
     */
    public function __construct(
        \Psr\Log\LoggerInterface $loggerInterface,
        \Triggmine\Connector\Helper\Data $data
    )
    {
        $this->helper   = $data;
        $this->logger   = $loggerInterface;
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
            
            $cart           = $observer->getEvent()->getCart();
            $cart_id        = null;
            $cart_items     = $cart->getQuote()->getAllVisibleItems();
            
            $data = array(
                'customer'    => $this->helper->getCustomerLoginData(),
                'order_id'    => $cart_id,
                'price_total' => sprintf('%01.2f', $cart->getQuote()->getGrandTotal()),
                'qty_total'   => $cart->getSummaryQty(),
                'products'    => array()
            );
            
            foreach($cart_items as $item)
            {
                if ($item->getProductType() == "simple")
                {
                    $item_id    = (integer) $item->getProductId();
                    $item_qty   = (integer) $item->getQty();
                    
                    $data['products'][] = $this->helper->getProduct($item_id, $item_qty);
                }
                else
                {
                    $data['products'][] = $this->helper->getProductCart($item);
                }
            }
            
            $this->helper->debug(json_encode($data));

            $res = $this->helper->sendCart($data);
            $this->helper->debug(json_encode($res));
                
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }

        return $this;
    }
    
    
}