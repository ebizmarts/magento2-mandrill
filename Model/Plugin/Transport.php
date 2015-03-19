<?php
/**
 * Author: info@ebizmarts.com
 * Date: 3/18/15
 * Time: 10:51 PM
 * File: Transport.php
 * Module: magento2
 */

namespace Ebizmarts\Mandrill\Model\Plugin;

class Transport
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface $objectManager
     */
    protected $_objectManager;
    /**
     * @var \Ebizmarts\Mandrill\Helper\Data
     */
    protected $_helper;
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Ebizmarts\Mandrill\Helper\Data $helper
    )
    {
        $this->_objectManager   = $objectManager;
        $this->_helper          = $helper;
    }
    public function beforeGetTransport($transport)
    {
        if($this->_helper->isActive())
        {
            $transport->setInstanceName('Ebizmarts\Mandrill\Model\Transport');
        }
    }
}