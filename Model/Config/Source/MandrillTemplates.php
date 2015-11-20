<?php
/**
 * Ebizmarts_Mandrill Magento Component
 *
 * @category Ebizmarts
 * @package Ebizmarts_Mandrill
 * @author Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date: 11/12/15 6:26 PM
 * @file: MandrillTemplates.php
 */

namespace Ebizmarts\Mandrill\Model\Config\Source;

class MandrillTemplates implements \Magento\Framework\Option\ArrayInterface
{
    protected $_api     = null;
    protected $_templates = null;
    protected $_helper  = null;

    /**
     * @param \Ebizmarts\Mandrill\Helper\Data $helper
     */
    public function __construct(\Ebizmarts\Mandrill\Helper\Data $helper)
    {
        $this->_helper  = $helper;
        $apiKey = $helper->getApiKey();
        if($apiKey) {
            try {
                $this->_api     = New \Mandrill($apiKey);
                $this->_templates = $this->_api->templates->getList();
            }
            catch(Mandrill_Error $e)
            {
                $this->_options = 'Invalid APIKEY';
            }
        }
    }

    public function toOptionArray()
    {
        /**
         * making filter by allowed cards
         */

        $options = [];

        foreach ($this->_templates as $template) {
                $options[] = ['value' => $template['name'], 'label' => $template['name']];
        }

        return $options;
    }
}