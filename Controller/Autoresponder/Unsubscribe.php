<?php
/**
 * Author: info@ebizmarts.com
 * Date: 8/3/15
 * Time: 12:15 PM
 * File: Unsubscribe.php
 * Module: magento2-mandrill
 */
namespace Ebizmarts\Mandrill\Controller\Autoresponder;

class Unsubscribe extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $_resultRedirectFactory;
    protected $messageManager;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        parent::__construct($context);
        $this->_objectManager = $objectManager;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultRedirectFactory = $context->getResultRedirectFactory();
        $this->messageManager = $context->getMessageManager();
    }
    public function execute()
    {
        $email  = $this->getRequest()->getParam('email', false);
        $list   = $this->getRequest()->getParam('list', false);
        $store  = $this->getRequest()->getParam('store', false);
        if($email && $list && $store)
        {
            $collection = $this->_objectManager->create('\Ebizmarts\Mandrill\Model\Unsubscribe')->getCollection();
            $collection->addFieldToFilter('main_table.email', array('eq' => $email))
                ->addFieldToFilter('main_table.list', array('eq' => $list))
                ->addFieldToFilter('main_table.store_id', array('eq' => $store));
            if($collection->getSize() == 0)
            {
                $unsubscribe = $this->_objectManager->create('\Ebizmarts\Mandrill\Model\Unsubscribe');
                $unsubscribe->setEmail($email)
                    ->setList($list)
                    ->setStoreId($store)
                    ->setUnsubscribedAt(date('Y-m-d H:i:s'));
                $unsubscribe->save();
                $this->messageManager->addNotice("You are unsubcribed from $list");
            }
            else
            {
                $this->messageManager->addNotice("You are already unsubcribed from $list");
            }
        }
        else {
            $this->messageManager->addNotice("Invalid url format");
        }
        $resultRedirect = $this->_resultRedirectFactory->create();
        $resultRedirect->setPath('/');
        return $resultRedirect;
    }
}