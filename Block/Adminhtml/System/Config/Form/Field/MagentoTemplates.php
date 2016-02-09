<?php
/**
 * Ebizmarts_Mandrill Magento Component
 *
 * @category Ebizmarts
 * @package Ebizmarts_Mandrill
 * @author Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date: 11/12/15 6:13 PM
 * @file: MagentoTemplates.php
 */

namespace Ebizmarts\Mandrill\Block\Adminhtml\System\Config\Form\Field;

class MagentoTemplates extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * @var array
     */
    protected $_magentoTemplates = [];
    /**
     * @var \Magento\Config\Model\Config\Source\Email\Template
     */
    protected $_magentoTemplatesSource;

    protected $_logger;

    /**
     * MagentoTemplates constructor.
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Config\Model\Config\Source\Email\Template $templateSource
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Email\Model\Template\Config $emailConfig,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_magentoTemplatesSource = $emailConfig;
        $this->_logger = $context->getLogger();
    }
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->_getTemplates() as $template) {
                $this->addOption($template['value'], $template['label']);
            }
        }
        return parent::_toHtml();
    }
    protected function _getTemplates()
    {
        if(!$this->_magentoTemplates)
        {
            $this->_magentoTemplates = $this->_magentoTemplatesSource->getAvailableTemplates();
        }
        return $this->_magentoTemplates;
    }

}