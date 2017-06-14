<?php

namespace Triggmine\Connector\Helper;

use Triggmine\Connector\Helper\Config as ConnectorConfig;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_storeManager;
    protected $_cartItemRepository;
    protected $_customerSession;
    protected $_customerRepository;
    protected $_cookieManager;
    protected $_productFactory;
    protected $_categoryFactory;
    protected $_request;
    protected $_debugEnabled;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Quote\Api\CartItemRepositoryInterface $cartItemRepositoryInterface,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->_storeManager        = $storeManagerInterface;
        $this->_cartItemRepository  = $cartItemRepositoryInterface;
        $this->_customerRepository  = $customerRepository;
        $this->_customerSession     = $customerSession;
        $this->_cookieManager       = $cookieManager;
        $this->_productFactory      = $productFactory;
        $this->_categoryFactory     = $categoryFactory;
        $this->_request             = $request;
        $this->_debugEnabled        = ConnectorConfig::XML_PATH_CONNECTOR_DEBUG_ENABLED;

        parent::__construct($context);
    }

    /**
     * Send Data to API
     * 
     * @param array $data
     * @param string $method
     * @return array
     */    
    public function apiClient($data, $method)
    {
        if ($this->getApiUrl() == "")
        {
            $res = array(
                "status"    => 0,
                "body"      => ""
            );
        }
        else
        {
            $target = "https://" . $this->getApiUrl() . "/" . $method;
    
            $data_string = json_encode($data);
            
            $ch = curl_init();
    
            curl_setopt($ch, CURLOPT_URL, $target);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);           
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(                  
                'Content-Type: application/json',
                'ApiKey: ' . $this->getApiKey(),
                'Content-Length: ' . strlen($data_string))
            );
            
            $res_json = curl_exec ($ch);
            
            $res = array(
                "status"    => curl_getinfo ($ch, CURLINFO_HTTP_CODE),
                "body"      => $res_json ? json_decode ($res_json, true) : curl_error ($ch)
            );
            
            curl_close ($ch);
        }
        
        return $res;
    }
    
    /**
     * Get API credentials Enabled
     * 
     * @return mixed
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(
            ConnectorConfig::XML_PATH_CONNECTOR_API_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    public function getApiUrl()
    {
        return $this->scopeConfig->getValue(
            ConnectorConfig::XML_PATH_CONNECTOR_API_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    public function getApiKey()
    {
        return $this->scopeConfig->getValue(
            ConnectorConfig::XML_PATH_CONNECTOR_API_KEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }
    
    public function getVersionPlugin()
    {
        return ConnectorConfig::VERSION_PLUGIN;
    }
    
    /**
     * Get Export credentials Enabled
     * 
     * @return mixed
     */
    public function isEnabledOrderExport()
    {
        return $this->scopeConfig->getValue(
            ConnectorConfig::XML_PATH_CONNECTOR_EXPORT_ORDER_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getExportDateFrom($format = 'Y-m-d H:i:s')
    {
        $dateVulue = $this->scopeConfig->getValue(
            ConnectorConfig::XML_PATH_CONNECTOR_EXPORT_ORDER_DATE_FROM,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        
        $dateFormat = date($format, strtotime(str_replace('/', '-', $dateVulue)));
        
        return $dateFormat;
    }

    public function getExportDateTo($format = 'Y-m-d H:i:s')
    {
        $dateVulue = $this->scopeConfig->getValue(
            ConnectorConfig::XML_PATH_CONNECTOR_EXPORT_ORDER_DATE_TO,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        
        $dateFormat = date($format, strtotime(str_replace('/', '-', $dateVulue)));
        
        return $dateFormat;
    }
    
    public function isEnabledCustomerExport()
    {
        return $this->scopeConfig->getValue(
            ConnectorConfig::XML_PATH_CONNECTOR_EXPORT_CUSTOMER_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCustomerDateFrom($format = 'Y-m-d H:i:s')
    {
        $dateVulue = $this->scopeConfig->getValue(
            ConnectorConfig::XML_PATH_CONNECTOR_EXPORT_CUSTOMER_DATE_FROM,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        
        $dateFormat = date($format, strtotime(str_replace('/', '-', $dateVulue)));
        
        return $dateFormat;
    }

    public function getCustomerDateTo($format = 'Y-m-d H:i:s')
    {
        $dateVulue = $this->scopeConfig->getValue(
            ConnectorConfig::XML_PATH_CONNECTOR_EXPORT_CUSTOMER_DATE_TO,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        
        $dateFormat = date($format, strtotime(str_replace('/', '-', $dateVulue)));
        
        return $dateFormat;
    }

    /**
     * Get store identifier
     *
     * @return  string
     */
    public function getStoreId()
    {
        return $this->_request->getParam('store', 0);
    }
    
    /**
     * Get website identifier
     *
     * @return  string
     */
    public function getWebsiteId()
    {   
        return $this->_request->getParam('website', 0);
    }
    
    public function log($data)
    {
        $this->_logger->info($data);
    }
    
    public function debug($data)
    {
        if ($this->_debugEnabled)
        {
            $this->_logger->debug($data);
        }
    }
    
    public function error($data)
    {
        $this->_logger->error($data);
    }

    protected function _getConfigValue($path, $contextScope, $contextScopeId = null)
    {

        $config = $this->scopeConfig->getValue(
            $path, $contextScope, $contextScopeId
        );

        return $config;
    }
    
    public function setDateFormat ($date_input, $format = "Y/m/d h:m:s")
    {
        $date_res = null;
        
        if ($date_input) {
            
            $date_unix      = strtotime($date_input);
            $date_res       = date($format, $date_unix);
        }
        
        return $date_res;
    }
    
    public function getProduct ($product_id, $qty = 1)
    {
        $product_item   = $this->_productFactory->create()->load($product_id);
        $product_desc   = substr(preg_replace("/\s{2,}/", " ", strip_tags($product_item->getDescription())), 0, 200);
        $categories     = $product_item->getCategoryIds();
        
        $product = array (
            "product_id"            => (integer) $product_id,
            "product_name"          => $product_item->getName(),
            "product_desc"          => $product_desc,
            "product_sku"           => $product_item->getData('sku'),
            "product_image"         => $this->getProdImg($product_item),
            "product_url"           => $product_item->getProductUrl(),
            "product_qty"           => (integer) $qty,
            "product_price"         => $product_item->getFinalPrice(),
            "product_total_val"     => $product_item->getFinalPrice() * $qty,
            "product_categories"    => array()
        );
        
        foreach ($categories as $categoryId) {
            
            $_category = $this->_categoryFactory->create()->load($categoryId);
            $product['product_categories'][] = $_category->getName();
        }
        
        return $product;
    }
    
    public function getProductCart ($item)
    {
        $item_id        = (integer) $item->getProductId();

        $product_item   = $this->_productFactory->create()->load($item_id);
        $product_desc   = substr(preg_replace("/\s{2,}/", " ", strip_tags($product_item->getDescription())), 0, 200);
        $categories     = $product_item->getCategoryIds();   

        $product = array (
            "product_id"            => $item_id,
            "product_name"          => $item->getName(),
            "product_desc"          => $product_desc,
            "product_sku"           => $item->getSku(),
            "product_image"         => $this->getProdImg($product_item),
            "product_url"           => $product_item->getProductUrl(),
            "product_qty"           => (integer) $item->getQty(),
            "product_price"         => $item->getPrice(),
            "product_total_val"     => $item->getPrice() * $item->getQty(),
            "product_categories"    => array()
        );
        
        foreach ($categories as $categoryId) {
            
            $_category = $this->_categoryFactory->create()->load($categoryId);
            $product['product_categories'][] = $_category->getName();
        }
        
        return $product;
    }

    public function getCartData($cart=null)
    {
        if (!(int)$cart->getQuote()->getId())
            return [];

        $items = $this->_cartItemRepository->getList($cart->getQuote()->getId());

        $data = array(
            'device_id'     => $this->getDeviceId(),
            'device_id_1'   => $this->getDeviceId_1(),
            'price_total'   => sprintf('%01.2f',$cart->getQuote()->getGrandTotal()),
            'qty_total'     => (int)$cart->getSummaryQty(),
            'products'      => array()
        );
        
        foreach($items as $item)
        {
            $allData = $this->_itemPool->getItemData($item);
            $itemData = array();
            $itemData['product_id'] = $item->getProductId();
            $itemData['product_name']  = $this->normalizeName($allData['product_name']);
            $itemData['product_sku']  = $item->getProduct()->getData('sku');
            $itemData['product_image'] = $allData['product_image']['src'];
            $itemData['product_url']  = $allData['product_url'];
            $itemData['product_qty']  = $allData['qty'];
            $itemData['product_price']  = sprintf('%01.2f',$this->_checkoutHelper->getPriceInclTax($item));
            $itemData['product_total_val']  = sprintf('%01.2f',$this->_checkoutHelper->getSubtotalInclTax($item));
            $data['products'][] = $itemData;
        }

        return  $data;
    }

    public function getDeviceId()
    {
        return $this->_cookieManager->getCookie('device_id');
    }

    public function getDeviceId_1()
    {
        return $this->_cookieManager->getCookie('device_id_1');
    }

    public function normalizeName($name)
    {
        return trim(preg_replace('/\s+/', ' ', $name));
    }
    
    public function getProdImg($product_item)
    {
        $url = "";
        
        if ($image = $product_item->getImage())
        {
            $http = (isset($_SERVER['HTTPS']) || isset($_SERVER['HTTPS']) && isset($_SERVER['HTTPS']) == "on" || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $url = $http . $_SERVER['SERVER_NAME'] . '/pub/media/catalog/product' . $image;
        }
        
        return $url;
    }
    
    public function getCustomerLoginData($customerId = null)
    {
        $customer = $this->getCustomer($customerId);

        $data = array(
            'device_id'             => $this->getDeviceId(),
            'device_id_1'           => $this->getDeviceId_1(),
            'customer_id'           => $customer ? $customer->getId() : "",
            'customer_first_name'   => $customer ? $customer->getFirstname() : "",
            'customer_last_name'    => $customer ? $customer->getLastname() : "",
            'customer_email'        => $customer ? $customer->getEmail() : "",
            'customer_date_created' => $customer ? $this->setDateFormat($customer->getCreatedAt()) : ""
        );

        return $data; 
    }
    
    public function getCustomerData($customerId = null)
    {
        $customer = $customerId ? $this->_customerRepository->getById($customerId) : null;

        $data = array(
            'customer_id'           => $customer ? $customer->getId() : "",
            'customer_first_name'   => $customer ? $customer->getFirstname() : "",
            'customer_last_name'    => $customer ? $customer->getLastname() : "",
            'customer_email'        => $customer ? $customer->getEmail() : "",
            'customer_date_created' => $customer ? $this->setDateFormat($customer->getCreatedAt()) : ""
        );

        return $data; 
    }

    public function getCustomer($customerId = null)
    {
        $customer = false;
        
        if ($customerId || $customerId = $this->_customerSession->getCustomerId())
        {
            $customer = $this->_customerRepository->getById($customerId);
        }
        
        return $customer;
    }


    /**
     * Send Data to API
     * 
     */ 
    public function sendSoftCheck($data)
    {
        return $this->apiClient($data, 'control/api/plugin/onDiagnosticInformationUpdated');
    }
    
    public function sendPageInit($data)
    {
        return $this->apiClient($data, 'api/events/navigation');
    }
    
    public function sendRegisterData($data)
    {
        return $this->apiClient($data, 'api/events/prospect/registration');
    }
    
    public function sendLoginData($data)
    {
        return $this->apiClient($data, 'api/events/prospect/login');
    }
    
    public function sendLogoutData($data)
    {
        return $this->apiClient($data, 'api/events/prospect/logout');
    }
    
    public function onConvertCartToOrder($data)
    {
        return $this->apiClient($data, 'api/events/order');
    }

    public function sendCart($data)
    {
        return $this->apiClient($data, 'api/events/cart');
    }
    
    public function exportOrderHistory($data)
    {
        return $this->apiClient($data, 'api/events/history');
    }
    
    public function exportCustomerHistory($data)
    {
        return $this->apiClient($data, 'api/events/history/prospects');
    }
}