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
 * @file: MandrillTemplates.php
 */

namespace Ebizmarts\Mandrill\Block\Adminhtml\System\Config\Form\Field;

class MandrillTemplates extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * @var array
     */
    protected $_mandrillTemplates = [];
    /**
     * @var \Ebizmarts\Mandrill\Model\Config\Source\MandrillTemplates
     */
    protected $_mandrillTemplatesSource;


    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Ebizmarts\Mandrill\Model\Config\Source\MandrillTemplates $templateSource,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_mandrillTemplatesSource = $templateSource;
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
        if(!$this->_mandrillTemplates)
        {
            $this->_mandrillTemplates = $this->_mandrillTemplatesSource->toOptionArray();
        }
        return $this->_mandrillTemplates;
    }

}