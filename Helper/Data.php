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
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;


    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_logger = $logger;
        parent::__construct($context);
    }
    public function getApiKey($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_APIKEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }
    public function isActive($store = null)
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ACTIVE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }
    /**
     * @codeCoverageIgnore
     */
    public function log($msg)
    {
        $this->_logger->info($msg);
    }
    public function getTestSender()
    {
        return $this->scopeConfig->getValue(
            'checkout/payment_failed/identity',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}