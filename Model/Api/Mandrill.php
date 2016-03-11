<?php
/**
 * Mandrill Magento Component
 *
 * @category Ebizmarts
 * @package Mandrill
 * @author Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date: 2/26/16 3:35 PM
 * @file: Mandrill.php
 */
namespace Ebizmarts\Mandrill\Model\Api;

class Mandrill
{
    /**
     * @var \Mandrill
     */
    protected $_api;
    /**
     * Mandrill constructor.
     * @param \Ebizmarts\Mandrill\Helper\Data $helper
     */
    public function __construct(
        \Ebizmarts\Mandrill\Helper\Data $helper
    )
    {
        $apiKey     = $helper->getApiKey();
        if($apiKey!='') {
            $this->_api = New \Mandrill($apiKey);
        }
    }
    public function getApi()
    {
        return $this->_api;
    }

}