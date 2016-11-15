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
     * @var \Magento\SalesRule\Model\Coupon
     */
    protected $_coupon;

    /**
     * @var \Magento\SalesRule\Model\Rule
     */
    protected $_rule;

    /**
     * @var \Ebizmarts\Mandrill\Model\Mailsent
     */
    protected $_mailsent;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_dateTime;

    /**
     * @var \Ebizmarts\Mandrill\Model\Unsubscribe
     */
    protected $_unsubscribe;

    /**
     * @var array
     */
    protected $_subscribed;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\SalesRule\Model\Coupon $coupon
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Ebizmarts\Mandrill\Model\Mailsent $mailsent
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Ebizmarts\Mandrill\Model\Unsubscribe $unsubscribe
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\SalesRule\Model\Coupon $coupon,
        \Magento\SalesRule\Model\Rule $rule,
        \Ebizmarts\Mandrill\Model\Mailsent $mailsent,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Ebizmarts\Mandrill\Model\Unsubscribe $unsubscribe
    )
    {
        $this->_logger = $context->getLogger();
        $this->_coupon = $coupon;
        $this->_rule = $rule;
        $this->_mailsent = $mailsent;
        $this->_dateTime = $dateTime;
        $this->_unsubscribe = $unsubscribe;
        $this->_subscribed = array();
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
            $coupon = $this->_coupon->loadByCode($couponCode);
            $rule = $this->_rule->load($coupon->getRuleId());
            $couponAmount = $rule->getDiscountAmount();
            switch ($rule->getSimpleAction()) {
                case 'cart_fixed':
                    $couponType = 1;
                    break;
                case 'by_percent':
                    $couponType = 2;
                    break;
                default:
                    $couponType = 0;
                    break;
            }
        } else {
            $couponType = 0;
            $couponAmount = 0;
        }
        $sent = $this->_mailsent;
        $date = $this->_dateTime;
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
            return $this->_checkSubscription($email, $list, $storeId);
        }else{
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
        $collection = $this->_unsubscribe->getCollection();
        $collection->addFieldToFilter('main_table.email', array('eq' => $email))
            ->addFieldToFilter('main_table.list', array('eq' => $list))
            ->addFieldToFilter('main_table.store_id', array('eq' => $storeId));
        if ($collection->getSize() == 0) {
            $this->_subscribed[$storeId][$list][$email] = 'true';
            return true;
        } else {
            $this->_subscribed[$storeId][$list][$email] = 'false';
            return false;
        }
    }
}