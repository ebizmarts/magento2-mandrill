<?php
/**
 * Author: info@ebizmarts.com
 * Date: 3/25/15
 * Time: 2:45 AM
 * File: TransportBuilder.php
 * Module: magento2
 */

namespace Ebizmarts\Mandrill\Model\Plugin;

class TransportBuilder
{
    /**
     * @var \Ebizmarts\Mandrill\Model\Message
     */
    protected $_message;
    /**
     * @var \Ebizmarts\Mandrill\Helper\Data
     */
    protected $_helper;

    /**
     * @param \Ebizmarts\Mandrill\Model\Message $message
     * @param \Ebizmarts\Mandrill\Helper\Data $helper
     */
    public function __construct(
        \Ebizmarts\Mandrill\Model\Message $message,
        \Ebizmarts\Mandrill\Helper\Data $helper
    )
    {
        $this->_message = $message;
        $this->_helper  = $helper;
    }
    public function beforeGetMessage(\Magento\Framework\Mail\Template\TransportBuilder $transportBuilder)
    {
        $this->_helper->log(__METHOD__);
        if($this->_helper->isActive())
        {
            $transportBuilder->setMessage($this->_message);
        }
    }
}