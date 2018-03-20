<?php

namespace Triggmine\Connector\Observer\Diagnostic;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;

class SoftCheck implements ObserverInterface
{
    protected $logger;
    protected $helper;
    protected $objectManager;
    protected $productMetadata;
    protected $messageManager;

    /**
     * Soft Check constructor.
     * @param \Psr\Log\LoggerInterface $loggerInterface
     * @param \Triggmine\Connector\Helper\Data $data
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Psr\Log\LoggerInterface $loggerInterface,
        \Triggmine\Connector\Helper\Data $data,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->logger           = $loggerInterface;
        $this->helper           = $data;
        $this->objectManager    = ObjectManager::getInstance();
        $this->productMetadata  = $this->objectManager->get('Magento\Framework\App\ProductMetadataInterface');
        $this->messageManager   = $messageManager;
    }
    
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $versionMage    = $this->productMetadata->getVersion();
        $versionPlugin  = $this->helper->getVersionPlugin();
        $datetime       = date('Y-m-d\TH:i:s');
        $status         = $this->helper->isEnabled() ? "1" : "0";
        
        try {
            
            $data = array(
                'dateCreated'       => $datetime,
                'diagnosticType'    => "ConfigurePlugin",
                'description'       => "Magento " . $versionMage . " Plugin " . $versionPlugin,
                'status'            => $status
            );
    
            $this->helper->debug(json_encode($data));

            $res = $this->helper->sendSoftCheck($data);
            
            if ($res["status"] === 503)
            {   
                $this->messageManager->addErrorMessage( __('Invalid API URL') );
            }
            else if ($res["status"] === 401)
            {   
                $this->messageManager->addErrorMessage( __('Invalid API KEY') );
            }
            
            $this->helper->debug(json_encode($res));
            
        } catch (\Exception $e) {
            
            $this->logger->critical($e);
        }
    }
}