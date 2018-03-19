<?php

namespace Triggmine\Connector\Controller\Adminhtml\Export;

class ExportProducts extends \Magento\Framework\App\Action\Action
{
    protected $_logger;
    
    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \Psr\Log\LoggerInterface $logger
        )
    {
        $this->_logger = $logger;
        
        parent::__construct($context);
    }
    
    public function execute()
    {
        // $this->_logger->debug('export reached');
    }
}