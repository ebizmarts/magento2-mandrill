<?php
/**
 * Ebizmarts_Mandrill Magento Component
 *
 * @category Ebizmarts
 * @package Ebizmarts_Mandrill
 * @author Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date: 11/12/15 4:09 PM
 * @file: Templatemap.php
 */

namespace Ebizmarts\Mandrill\Block\Adminhtml\System\Config\Form\Field;

class Templatemap extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $_elementFactory;

    /**
     * @var \Ebizmarts\Mandrill\Block\Adminhtml\System\Config\Form\Field\MagentoTemplates
     */
    protected $_magentoTemplates = null;
    /**
     * @var \Ebizmarts\Mandrill\Block\Adminhtml\System\Config\Form\Field\MandrillTemplates
     */
    protected $_mandrillTemplates = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        array $data = []
    )
    {
        $this->_elementFactory  = $elementFactory;
        parent::__construct($context,$data);
    }
    protected function _construct()
    {
        $this->addColumn(
            'magento',
            [
                'label' => __('Magento Template'),
                'renderer'  => $this->getMagentoTemplateRenderer()
            ]
        );
        $this->addColumn(
            'mandrill',
            [
                'label' => __('Mandrill Template'),
                'renderer'  => $this->getMandrillTemplateRenderer()
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
        parent::_construct();
    }
    protected function getMagentoTemplateRenderer()
    {
        if(!$this->_magentoTemplates)
        {
            $this->_magentoTemplates = $this->getLayout()->createBlock(
                '\Ebizmarts\Mandrill\Block\Adminhtml\System\Config\Form\Field\MagentoTemplates',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_magentoTemplates;
    }
    protected function getMandrillTemplateRenderer()
    {
        if(!$this->_mandrillTemplates)
        {
            $this->_mandrillTemplates = $this->getLayout()->createBlock(
                '\Ebizmarts\Mandrill\Block\Adminhtml\System\Config\Form\Field\MandrillTemplates',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_mandrillTemplates;
    }
}