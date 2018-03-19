<?php
namespace Triggmine\Connector\Block\Adminhtml\System\Config;

class ProductExport extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Template path
     *
     * @var string
     */
    protected $_template = 'system/config/product-export.phtml';
    
    public $_productExportStep;
    
    public $_pages;
    
    protected $scopeConfig;
    
    protected $productCollectionFactory;
    
    protected $productStatus;
    
    protected $productVisibility;
    
    public function __construct(
            \Magento\Backend\Block\Template\Context $context,
            // \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
            // \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
            \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
            \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
            \Magento\Catalog\Model\Product\Visibility $productVisibility,
            array $data = []
        )
    {
        parent::__construct($context, $data);
        
        $this->_productExportStep = 20;
        $this->_pages = 0;
        // $this->scopeConfig = $scopeConfigInterface;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
    }
    
    /**
     * @return \Magento\Framework\DataObject[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProducts()
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->productCollectionFactory->create();
    
        return $collection->getItems();
    }
    
    /**
     * Render fieldset html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // $pluginSetUp   = Mage::app()->getWebsite($websiteId)->getConfig('triggmine/settings/plugin_set_up');
        // $this->_websiteId = $this->scopeConfig->getValue('triggmine_connector_api_credentials/api/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
        // $productExport = Mage::getStoreConfig('triggmine/triggmine_product_export/export', $storeId);
        // $pluginEnabled = Mage::helper('integrationmodule/data')->isEnabled();
        
        $productsCollection = $this->getProducts();
        $productsCount = count($productsCollection);
        
        if ( $productsCount % $this->_productExportStep == 0 )
        {
            $this->_pages = floor( $productsCount / $this->_productExportStep );
        }
        else
        {
            $this->_pages = floor( $productsCount / $this->_productExportStep ) + 1;
        }
            
        // $this->_logger->debug(count($productsCollection));
        
        $columns = $this->getRequest()->getParam('website') || $this->getRequest()->getParam('store') ? 5 : 4;
        
        return $this->_decorateRowHtml($element, "<td colspan='{$columns}'>" . $this->toHtml() . '</td>');
    }
}