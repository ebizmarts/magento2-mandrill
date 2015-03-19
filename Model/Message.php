<?php
/**
 * Author: info@ebizmarts.com
 * Date: 3/18/15
 * Time: 3:44 PM
 * File: Message.php
 * Module: magento2
 */

namespace Ebizmarts\Mandrill\Model;


class Message extends \Zend_Mail implements \Magento\Framework\Mail\MessageInterface
{
    protected $_subject     = null;
    protected $_bodyHtml    = null;
    protected $_bodyText    = null;
    protected $_messageType = self::TYPE_TEXT;
    protected $_bcc         = array();
    protected $_to          = array();
    protected $_att         = array();
    protected $_headers     = array();
    protected $_from        = null;
    protected $_fromName    = null;

    public function setSubject($subject)
    {
        $this->_subject = $subject;
    }
    public function getSubject()
    {
        return $this->_subject;
    }
    public function setBody($body)
    {
        $this->_messageType == self::TYPE_TEXT ? $this->_bodyText = $body : $this->_bodyHtml = $body;

    }
    public function getBody()
    {
        return $this->_messageType == self::TYPE_TEXT ? $this->_bodyText : $this->_bodyHtml;
    }
    public function setFrom($fromAddress,$name = null)
    {
        $this->_from        = $fromAddress;
        $this->_fromName    = $name;
    }
    public function addTo($toAddress)
    {
        if(is_array($toAddress))
        {
            foreach($toAddress as $address)
            {
                array_push($this->_to,$address);
            }
        }
        else {
            array_push($this->_to,$toAddress);
        }
    }
    public function addCc($ccAddress)
    {
        if(is_array($ccAddress))
        {
            foreach($ccAddress as $address)
            {
                array_push($this->_to,$address);
            }
        }
        else {
            array_push($this->_to,$ccAddress);
        }

    }
    public function addBcc($bccAddress)
    {
        if(is_array($bccAddress))
        {
            foreach($bccAddress as $address)
            {
                array_push($this->_bcc,$address);
            }
        }
        else {
            array_push($this->_bcc,$bccAddress);
        }
    }
    public function setMessageType($type)
    {
        $this->_messageType = $type;
        return $this;
    }
    public function createAttachment($body,
                                     $mimeType    = \Zend_Mime::TYPE_OCTETSTREAM,
                                     $disposition = \Zend_Mime::DISPOSITION_ATTACHMENT,
                                     $encoding    = \Zend_Mime::ENCODING_BASE64,
                                     $filename    = null)
    {
        $att = array('type' => $mimeType,'name' => $filename,'content'=> base64_encode($body));
        array_push($this->_att,$att);
    }
    public function getAttachments()
    {
        return $this->_att;
    }
    public function addHeader($name, $value, $append = false)
    {
        $prohibit = array('to', 'cc', 'bcc', 'from', 'subject',
            'reply-to', 'return-path',
            'date', 'message-id',
        );
        if (in_array(strtolower($name), $prohibit)) {
            /**
             * @see Zend_Mail_Exception
             */
            #require_once 'Zend/Mail/Exception.php';
            throw new Zend_Mail_Exception('Cannot set standard header from addHeader()');
        }

        $this->_header[$name] = $value;

        return $this;
    }
    public function getHeaders()
    {
        if(isset($this->_headers[0])) {
            return $this->_headers[0];
        }
        else {
            return null;
        }
    }
    protected function _filterEmail($email)
    {
        $rule = array("\r" => '',
            "\n" => '',
            "\t" => '',
            '"'  => '',
            ','  => '',
            '<'  => '',
            '>'  => '',
        );

        return strtr($email, $rule);
    }
    protected function _filterName($name)
    {
        $rule = array("\r" => '',
            "\n" => '',
            "\t" => '',
            '"'  => "'",
            '<'  => '[',
            '>'  => ']',
        );

        return trim(strtr($name, $rule));
    }

    public function setReplyTo($email, $name = null)
    {
        $email = $this->_filterEmail($email);
        $name  = $this->_filterName($name);
        $this->_headers[] = array('Reply-To'=>sprintf('%s <%s>',$name,$email));
        return $this;
    }

    public function send()
    {
        $email = array();
        foreach($this->_to as $to) {
            $email['to'][] = array(
                'email' => $to
            );
        }
        foreach($this->_bcc as $bcc) {
            $email['to'][] = array(
                'email' => $bcc,
                'type' => 'bcc'
            );
        }
        $email['subject'] = $this->_subject;
        if(isset($this->_fromName)) {
            $email['from_name'] = $this->_fromName;
        }
        $email['from_email'] = $this->_from;
        if($headers = $this->getHeaders()) {
            $email['headers'] = $headers;
        }
        if($att = $this->getAttachments()) {
            $email['attachments'] = $att;
        }
        if($this->_bodyHtml) {
            $email['html'] = $this->_bodyHtml;
        }
        if($this->_bodyText) {
            $email['text'] = $this->_bodyText;
        }

        try {
            $result = $this->messages->send($email);
        }
        catch(Exception $e ) {
//            Mage::logException( $e );
            return false;
        }
        return true;
    }

}