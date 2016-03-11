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
namespace Ebizmarts\Mandrill\Block\Adminhtml;

class SendTestEmail extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_template    = 'sendemail.phtml';
    /**
     * @codeCoverageIgnore
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        $originalData = $element->getOriginalData();

        $label = $originalData['button_label'];

        $this->addData(array(
            'button_label' => __($label),
            'button_url'   => $this->getUrl('mandrilltest/email/test'),
            'html_id' => $element->getHtmlId(),
        ));
        return $this->_toHtml();
    }
}