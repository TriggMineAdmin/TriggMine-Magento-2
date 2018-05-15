<?php

namespace Triggmine\Connector\Controller\Adminhtml\Export;

class ExportProducts extends \Magento\Framework\App\Action\Action
{
    protected $_logger;
    
    protected $productCollectionFactory;
    
    protected $productFactory;
    
    protected $categoryRepository;
    
    protected $storeManager;
    
    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \Magento\Framework\App\Request\Http $request,
            \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
            \Magento\Catalog\Model\ProductFactory $productFactory,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\Catalog\Model\CategoryRepository $categoryRepository,
            \Psr\Log\LoggerInterface $logger
        )
    {
        $this->_logger = $logger;
        
        $this->request = $request;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productFactory = $productFactory;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        
        parent::__construct($context);
    }
    
    /**
     * @return \Magento\Framework\DataObject[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProducts($pageSize = 20, $page = 1)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->productCollectionFactory->create();
        $collection->setPageSize($pageSize)->setCurPage($page);
    
        return $collection->getItems();
    }
    
    public function getProdImg($product)
    {
        $url = "";
        if ($image = $product->getImage())
        {
            $http = (isset($_SERVER['HTTPS']) || isset($_SERVER['HTTPS']) && isset($_SERVER['HTTPS']) == "on" || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $url = $http . $_SERVER['SERVER_NAME'] . '/media/catalog/product' . $image;
        }
        return $url;
    }
    
    public function getProductHistory($pageSize = 20, $page = 1)
    {
        $products = $this->getProducts($pageSize, $page);
        
        foreach ($products as $productId => $value)
        {
            $productItem = $this->productFactory->create()->load($productId);
            
            // prepare categories array
            $productCategory = array();
            $categories = $productItem->getCategoryIds();
            
            // foreach ($categories as $categoryId)
            // {
            //     $category     = Mage::getModel('catalog/category')->load($categoryId);
            //     $categoryName = $category->getName();
                
            //     // this structure is needed on the backend
            //     $productCategory[] = array(
            //         'product_category_type' => array(
            //             'category_id'   => $categoryId ? $categoryId : "",
            //             'category_name' => $categoryName
            //         )
            //     );
            // }
            
            $productStatus = true; 
            if ((int) $productItem->getStatus() !== 1)
            {
                $productStatus = false;
            }
            
            // complete product array
            $productData = array (
                    'product_id'               => $productItem->getId(),
                    'parent_id'                => $productItem->getId(),
                    'product_name'             => $productItem->getName() ? $productItem->getName() : "",
                    'product_desc'             => $productItem->getDescription(),
                    'product_create_date'      => $productItem->getCreatedAt(),
                    'product_sku'              => $productItem->getSku(),
                    'product_image'            => $this->getProdImg($productItem),
                    //'product_url'              => $this->getProdUrl($productItem, $storeId),
                    'product_qty'              => (int) $productItem->getQty(),
                    'product_default_price'    => $productItem->getPrice(),
                    //'product_prices'           => $productPrice,
                    'product_categories'       => $productCategory,
                    //'product_relations'        => $productRelation,
                    'product_is_removed'       => null,
                    'product_is_active'        => $productStatus,
                    'product_active_from'      => $productItem->getCustomDesignFrom(),
                    'product_active_to'        => $productItem->getCustomDesignTo(),
                    'product_show_as_new_from' => $productItem->getNewsFromDate(),
                    'product_show_as_new_to'   => $productItem->getNewsToDate()
                );
            
            $this->_logger->debug(json_encode($productData));
        }
    }
    
    public function execute()
    {
        $pageSize   = (int) $this->request->getParam('pageSize');
        $page       = (int) $this->request->getParam('page');
        $pagesTotal = (int) $this->request->getParam('pagesTotal');
        
        //$productsCollection = $this->getProducts();
        
        $data = $this->getProductHistory($pageSize, $page);
        
        $this->_logger->debug($this->request->getParam('page'));
    }
}