<?php
namespace Triggmine\Connector\Block\Adminhtml\System\Config;

class Scope extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Template path
     *
     * @var string
     */
    protected $_template = 'system/config/scope.phtml';
    
    protected $_websiteFactory;
    
    protected $_storeGroupFactory;
    
    protected $_storeFactory;
    
    protected $_element;
    
    protected $_blockFactory;
    
    public $_currentWebsiteId;
    
    public $_block;
    
    public function __construct(
            \Magento\Backend\Block\Template\Context $context,
            \Magento\Store\Model\WebsiteFactory $websiteFactory,
            \Magento\Store\Model\GroupFactory $storeGroupFactory,
            \Magento\Store\Model\StoreFactory $storeFactory,
            \Magento\Cms\Model\BlockFactory $blockFactory,
            array $data = []
        )
    {
        parent::__construct($context, $data);
        $this->_websiteFactory = $websiteFactory;
        $this->_storeGroupFactory = $storeGroupFactory;
        $this->_storeFactory = $storeFactory;
        
        $this->_block = $this->getLayout()->createBlock('Magento\Backend\Block\Store\Switcher');
    }
    
    /**
     * Render fieldset html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $websites = $this->_block->getWebsites();
        
        // $this->_logger->debug($this->_block->isWebsiteSwitchEnabled());
        
        // TO DO
        // add flag here to know whether to render website or store selector in phtml
        
        $this->_currentWebsiteId = (int) $this->getRequest()->getParam('website', 0);
        
        $this->_element = $element;
        return $this->toHtml();
    }
}