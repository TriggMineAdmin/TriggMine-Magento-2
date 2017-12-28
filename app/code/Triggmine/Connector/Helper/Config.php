<?php

namespace Triggmine\Connector\Helper;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MODULE_NAME           = 'Triggmine_Connector';
    const VERSION_PLUGIN        = '3.0.24';
    
    /** API SECTION */
    const XML_PATH_CONNECTOR_API_ENABLED        = 'triggmine_connector_api_credentials/api/enabled';
    const XML_PATH_CONNECTOR_API_URL            = 'triggmine_connector_api_credentials/api/api_url';
    const XML_PATH_CONNECTOR_API_KEY            = 'triggmine_connector_api_credentials/api/api_key';
    
    /** EXPORT ORDER SECTION */
    const XML_PATH_CONNECTOR_EXPORT_ORDER_ENABLED     = 'triggmine_connector_export/export/enabled';
    const XML_PATH_CONNECTOR_EXPORT_ORDER_DATE_FROM   = 'triggmine_connector_export/export/startdate';
    const XML_PATH_CONNECTOR_EXPORT_ORDER_DATE_TO     = 'triggmine_connector_export/export/enddate';
    
    /** EXPORT CUSTOMER SECTION */
    const XML_PATH_CONNECTOR_EXPORT_CUSTOMER_ENABLED     = 'triggmine_connector_customer_export/customer_export/enabled';
    const XML_PATH_CONNECTOR_EXPORT_CUSTOMER_DATE_FROM   = 'triggmine_connector_customer_export/customer_export/startdate';
    const XML_PATH_CONNECTOR_EXPORT_CUSTOMER_DATE_TO     = 'triggmine_connector_customer_export/customer_export/enddate';
    
    /** DEVELOPER SECTION */
    const XML_PATH_CONNECTOR_DEBUG_ENABLED      = false;
}