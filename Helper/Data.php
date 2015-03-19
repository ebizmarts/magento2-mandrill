<?php
/**
 * Author: info@ebizmarts.com
 * Date: 3/16/15
 * Time: 1:42 PM
 * File: Data.php
 * Module: magento2
 */

namespace Ebizmarts\Mandrill\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const XML_PATH_ACTIVE           = 'mandrill/general/active';
    const XML_PATH_APIKEY           = 'mandrill/general/apikey';

    public function getApiKey($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_APIKEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }
    public function isActive($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ACTIVE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

}