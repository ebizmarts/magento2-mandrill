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
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $_ruleRepository;

    /**
     * @var \Ebizmarts\Mandrill\Model\Mailsent
     */
    protected $_mailsent;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
     */
    protected $_dateFactory;

    /**
     * @var \Ebizmarts\Mandrill\Model\Unsubscribe
     */
    protected $_unsubscribe;

    /**
     * @var array
     */
    protected $_subscribed;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\SalesRule\Model\Coupon $coupon
     * @param \Magento\SalesRule\Model\RuleRepository $ruleRepository
     * @param \Ebizmarts\Mandrill\Model\Mailsent $mailsent
     * @param \Ebizmarts\Mandrill\Model\Unsubscribe $unsubscribe
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\SalesRule\Model\Coupon $coupon,
        \Magento\SalesRule\Model\RuleRepository $ruleRepository,
        \Ebizmarts\Mandrill\Model\Mailsent $mailsent,
        \Ebizmarts\Mandrill\Model\Unsubscribe $unsubscribe
    ) {
    
        $this->_logger = $context->getLogger();
        $this->_coupon = $coupon;
        $this->_ruleRepository = $ruleRepository;
        $this->_mailsent = $mailsent;
        $this->_dateFactory = $dateFactory;
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
     * @param string|int $store
     * @return bool
     */
    public function isMandrillEnabled($store = null)
    {
        return (1 === (int)$this->isActive($store));
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
    public function saveMail($mailType, $mail, $name, $couponCode, $storeId)
    {
        if ($couponCode != '') {
            $coupon = $this->_coupon->loadByCode($couponCode);
            $rule = $this->_ruleRepository->getById($coupon->getRuleId());
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
        $date = $this->_dateFactory->create()->gmtDate();
        $sent->setMailType($mailType)
            ->setStoreId($storeId)
            ->setCustomerEmail($mail)
            ->setCustomerName($name)
            ->setCouponNumber($couponCode)
            ->setCouponType($couponType)
            ->setCouponAmount($couponAmount)
            ->setSentAt($date)
            ->save();
    }
    public function isSubscribed($email, $list, $storeId)
    {
        $subscribed = $this->_subscribed;
        if (!isset($subscribed[$storeId][$list][$email])) {
            return $this->_checkSubscription($email, $list, $storeId);
        } else {
            return $subscribed[$storeId][$list][$email];
        }
    }

    /**
     * @param $email
     * @param $list
     * @param $storeId
     * @return bool
     */
    private function _checkSubscription($email, $list, $storeId)
    {
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
