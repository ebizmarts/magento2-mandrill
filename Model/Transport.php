<?php
/**
 * Author: info@ebizmarts.com
 * Date: 7/8/15
 * Time: 7:07 PM
 * File: Transport.php
 * Module: magento2-mandrill
 */
namespace Ebizmarts\Mandrill\Model;

class Transport implements \Magento\Framework\Mail\TransportInterface
{
    /**
     * @var \Ebizmarts\Mandrill\Model\Message
     */
    protected $_message;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    /**
     * @var \Ebizmarts\Mandrill\Helper\Data
     */
    protected $_helper;
    /**
     * @param \Magento\Framework\Mail\MessageInterface $message
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Ebizmarts\Mandrill\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\Mail\MessageInterface $message,
        \Psr\Log\LoggerInterface $logger,
        \Ebizmarts\Mandrill\Helper\Data $helper
    )
    {
        $this->_message = $message;
        $this->_logger  = $logger;
        $this->_helper  = $helper;
    }
    public function sendMessage()
    {
        $apiKey     = $this->_helper->getApiKey();
        $api        = New \Mandrill($apiKey);
        $message    = array(
            'subject' => $this->_message->getSubject(),
            'from_name' => $this->_message->getFromName(),
            'from_email'=> $this->_message->getFrom(),
        );
        foreach($this->_message->getTo() as $to)
        {
            $message['to'][] = array(
                'email' => $to
            );
        }
        foreach($this->_message->getBcc() as $bcc)
        {
            $message['to'][] = array(
                'email' => $bcc,
                'type' => 'bcc'
            );
        }
        if($att = $this->_message->getAttachments()) {
            $message['attachments'] = $att;
        }
        if($headers = $this->_message->getHeaders()) {
            $message['headers'] = $headers;
        }
        switch($this->_message->getType())
        {
            case \Magento\Framework\Mail\MessageInterface::TYPE_HTML:
                $message['html'] = $this->_message->getBody();
                break;
            case \Magento\Framework\Mail\MessageInterface::TYPE_TEXT:
                $message['text'] = $this->_message->getBody();
                break;
        }
        $api->call('messages/send',array("message" => $message));
        return;
    }
}