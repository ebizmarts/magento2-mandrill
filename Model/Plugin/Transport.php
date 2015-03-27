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
     * @var \Ebizmarts\Mandrill\Helper\Data
     */
    protected $_helper;

    /**
     * @param \Ebizmarts\Mandrill\Helper\Data $helper
     */
    public function __construct(
        \Ebizmarts\Mandrill\Helper\Data $helper
    )
    {
        $this->_helper          = $helper;
    }
    public function beforeCreate($transport)
    {
        if($this->_helper->isActive())
        {
            $transport->setInstanceName('Ebizmarts\Mandrill\Model\Transport');
        }
    }
}