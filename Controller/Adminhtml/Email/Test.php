<?php
/**
 * Author: info@ebizmarts.com
 * Date: 7/9/15
 * Time: 1:33 AM
 * File: Test.php
 * Module: magento2-mandrill
 */
namespace Ebizmarts\Mandrill\Controller\Adminhtml\Email;

use Magento\Framework\Object;
use Magento\Framework\Controller\ResultFactory;


class Test extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    )
    {
        parent::__construct($context);
        $this->_transportBuilder = $transportBuilder;
    }
    public function execute()
    {
        $email      = $this->getRequest()->getParam('email');
//        $this->_objectManager->get('Ebizmarts\Mandrill\Helper\Data')->sendTestEmail($email);
        $template   = "mandrill_test_template";
        $transport  = $this->_transportBuilder->setTemplateIdentifier($template)
            ->setFrom($this->_objectManager->get('Ebizmarts\Mandrill\Helper\Data')->getTestSender())
            ->addTo($email)
            ->setTemplateVars([])
            ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => 1])
            ->getTransport();
        $transport->sendMessage();
        $response   = new Object();
        $response->setError(0);
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($response->toArray());
        return $resultJson;
    }
}