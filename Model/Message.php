<?php
/**
 * Ebizmarts_Mandrill Magento JS component
 *
 * @category    Ebizmarts
 * @package     Ebizmarts_Mandrill
 * @author      Ebizmarts Team <info@ebizmarts.com>
 * @copyright   Ebizmarts (http://ebizmarts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


namespace Ebizmarts\Mandrill\Model;

class Message implements \Magento\Framework\Mail\MessageInterface
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
    protected $_transport    = null;

    /**
     * Message constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Ebizmarts\Mandrill\Helper\Data $helper
     * @param Api\Mandrill $api
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Ebizmarts\Mandrill\Helper\Data $helper,
        \Ebizmarts\Mandrill\Model\Api\Mandrill $api
    )
    {
        $this->_transport = new Transport($this,$logger,$helper,$api);
    }
    public function setSubject($subject)
    {
        $this->_subject = $subject;
        return $this;
    }
    public function getSubject()
    {
        return $this->_subject;
    }
    public function setBody($body)
    {
        $this->_messageType == self::TYPE_TEXT ? $this->_bodyText = $body : $this->_bodyHtml = $body;
        return $this;
    }
    public function getBody()
    {
        return $this->_messageType == self::TYPE_TEXT ? $this->_bodyText : $this->_bodyHtml;
    }
    public function setFrom($fromAddress,$name = null)
    {
        $this->_from        = $fromAddress;
        $this->_fromName    = $name;
        return $this;
    }
    public function getFromName()
    {
        return $this->_fromName;
    }
    public function getFrom()
    {
        return $this->_from;
    }
    public function getType()
    {
        return $this->_messageType;
    }
    public function getTo()
    {
        return $this->_to;
    }
    public function getBcc()
    {
        return $this->_bcc;
    }
    public function addTo($toAddress,$name = null)
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
        return $this;
    }
    public function addCc($ccAddress,$name = null)
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
        return $this;
    }
    public function addBcc($bccAddress,$name = null)
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
        return $this;
    }
    public function setMessageType($type)
    {
        $this->_messageType = $type;
        return $this;
    }
    public function getMessageType()
    {
        return $this->_messageType;
    }
    public function createAttachment($body,
                                     $mimeType    = \Zend_Mime::TYPE_OCTETSTREAM,
                                     $disposition = \Zend_Mime::DISPOSITION_ATTACHMENT,
                                     $encoding    = \Zend_Mime::ENCODING_BASE64,
                                     $filename    = null)
    {
        $att = array('type' => $mimeType,'name' => $filename,'content'=> base64_encode($body));
        array_push($this->_att,$att);
        return $this;
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
            throw new \Magento\Framework\Exception\LocalizedException(__('Cannot set standard header from addHeader()'));
        }

        $this->_headers[$name] = $value;
        return $this;
    }
    public function getHeaders()
    {
        return $this->_headers;
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

    public function send($transport = null)
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
            $result = $this->_transport->sendMessage();
        }
        catch(\Exception $e ) {
            return false;
        }
        return true;
    }

}