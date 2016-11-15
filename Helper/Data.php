<?php
/**
 * Ebizmarts_Mandrill Magento JS component
 *
 * @category    Ebizmarts
 * @package     Ebizmarts_Mandrill
 * @author      Ebizmarts Team <info@ebizmarts.com>
 * @copyright   Ebizmarts (http://ebizmarts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    )
    {
        $this->_logger = $context->getLogger();;
        parent::__construct($context);
    }

    /**
     * @param null $store
     * @return mixed
     */
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

    /**
     * @return mixed
     */
    public function getTestSender()
    {
        return $this->scopeConfig->getValue(
            'checkout/payment_failed/identity',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $mailType
     * @param $mail
     * @param $name
     * @param $couponCode
     * @param $storeId
     */
    public function saveMail($mailType,$mail,$name,$couponCode,$storeId)
    {
        if ($couponCode != '') {
            $coupon = $this->_objectManager->create('Magento\SalesRule\Model\Coupon')->loadByCode($couponCode);
            $rule = $this->_objectManager->create('Magento\SalesRule\Model\Rule')->load($coupon->getRuleId());
            $couponAmount = $rule->getDiscountAmount();
            switch ($rule->getSimpleAction()) {
                case 'cart_fixed':
                    $couponType = 1;
                    break;
                case 'by_percent':
                    $couponType = 2;
                    break;
            }
        } else {
            $couponType = 0;
            $couponAmount = 0;
        }
        $sent = $this->_objectManager->create('Ebizmarts\Mandrill\Model\Mailsent');
        $date = $this->_objectManager->create('\Magento\Framework\Stdlib\DateTime\DateTime');
        $sent->setMailType($mailType)
            ->setStoreId($storeId)
            ->setCustomerEmail($mail)
            ->setCustomerName($name)
            ->setCouponNumber($couponCode)
            ->setCouponType($couponType)
            ->setCouponAmount($couponAmount)
            ->setSentAt($date->gmtDate())
            ->save();
    }
    public function isSubscribed($email, $list, $storeId)
    {
        $subscribed = $this->_subscribed;
        $isSubscribed = $subscribed[$storeId][$list][$email];
        if(!isset($isSubscribed)) {
            $this->log('not cached');
            return $this->_checkSubscription($email, $list, $storeId);
        }else{
            $this->log('cached');
            return $isSubscribed;
        }
    }

    /**
     * @param $email
     * @param $list
     * @param $storeId
     * @return bool
     */
    private function _checkSubscription($email, $list, $storeId){
        $collection = $this->_objectManager->create('\Ebizmarts\Mandrill\Model\Unsubscribe')->getCollection();
        $collection->addFieldToFilter('main_table.email', array('eq' => $email))
            ->addFieldToFilter('main_table.list', array('eq' => $list))
            ->addFieldToFilter('main_table.store_id', array('eq' => $storeId));
        if ($collection->getSize() == 0) {
            $this->_subscribed[$storeId][$list][$email] = 'true';
            $this->log('true');
            return true;
        } else {
            $this->_subscribed[$storeId][$list][$email] = 'false';
            $this->log('false');
            return false;
        }
    }
}