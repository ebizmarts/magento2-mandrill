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
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
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