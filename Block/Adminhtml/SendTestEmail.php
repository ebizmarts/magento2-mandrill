<?php
/**
 * Author: info@ebizmarts.com
 * Date: 7/9/15
 * Time: 12:22 AM
 * File: SendTestEmail.php
 * Module: magento2-mandrill
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