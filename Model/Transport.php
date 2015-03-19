<?php
/**
 * Author: info@ebizmarts.com
 * Date: 3/18/15
 * Time: 5:17 PM
 * File: Transport.php
 * Module: magento2
 */

namespace Ebizmarts\Mandrill\Model;

class Transport implements \Magento\Framework\Mail\TransportInterface
{
    protected $_message;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_logger  = $logger;
    }

    public function sendMessage()
    {
        $this->_logger->info(__METHOD__);
        return;
        //die('aleluya');
    }
}