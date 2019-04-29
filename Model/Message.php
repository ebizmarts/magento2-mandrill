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

class Message extends \Magento\Framework\Mail\Message implements \Magento\Framework\Mail\MailMessageInterface
{
    private $subject     = null;
    private $mandrillBodyHtml    = null;
    private $mandrillBodyText    = null;
    private $mandrillMessageType = self::TYPE_TEXT;
    private $bcc         = array();
    private $mandrillTo          = array();
    private $att         = array();
    private $mandrillHeaders     = array();
    private $mandrillFrom        = null;
    private $_fromName    = null;

    /** @var \Ebizmarts\Mandrill\Helper\Data */
    private $mandrillHelper;

    /**
     * Message constructor.
     * @param \Ebizmarts\Mandrill\Helper\Data $helper
     */
    public function __construct(\Ebizmarts\Mandrill\Helper\Data $helper)
    {
        $this->mandrillHelper = $helper;
        parent::__construct();
    }

    public function setSubject($subject)
    {
        if ($this->mandrillHelper->isMandrillEnabled()) {
            $this->subject = $subject;
        } else {
            parent::setSubject($subject);
        }

        return $this;
    }

    public function getSubject()
    {
        if ($this->mandrillHelper->isMandrillEnabled()) {
            return $this->subject;
        } else {
            return parent::getSubject();
        }
    }

    public function setBodyHtml($html)
    {
        if ($this->mandrillHelper->isMandrillEnabled()){
            $this->mandrillBodyHtml = $html;
            $this->mandrillMessageType = self::TYPE_HTML;
            return $this;
        }else{
            return parent::setBodyHtml($html);
        }

    }

    public function setBodyText($text)
    {
        if ($this->mandrillHelper->isMandrillEnabled()){
            $this->mandrillMessageType = self::TYPE_TEXT;
            $this->mandrillBodyText = $text;
            return $this;
        }else{
            return parent::setBodyText($text);
        }

    }
    public function getRawMessage()
    {
        if ($this->mandrillBodyText) {
            return $this->convertToString($this->mandrillBodyText);
        } elseif ($this->mandrillBodyHtml) {
            return $this->convertToString($this->mandrillBodyHtml);
        } else {
            return parent::getRawMessage();
        }
    }

    /**
     * Converts item to string.
     * If item cannot be converted than returns empty string.
     *
     * This method is used to fix an error when sending e-mail when 'Mandrill' is disabled.
     *
     * TODO:
     *  sync repository from original and remove this method and
     *  its use in the {@link \Ebizmarts\Mandrill\Model\Message::getRawMessage}
     *  when bug will be fixed in the original repository.
     *
     * @param mixed $item
     *
     * @return string
     *
     * @see https://github.com/ebizmarts/magento2-mandrill/issues/100
     */
    private function convertToString($item): string
    {
        if(is_null($item) || is_scalar($item)) {
            return (string) $item;
        } elseif (is_object($item)) {
            if (method_exists($item, 'toString')) {
                return (string) $item->toString();
            } elseif(method_exists($item, '__toString')) {
                return (string) $item;
            }
        }
        return '';
    }

    public function getBody()
    {
        if ($this->mandrillHelper->isMandrillEnabled()) {
            return $this->mandrillMessageType == self::TYPE_TEXT ? $this->mandrillBodyText : $this->mandrillBodyHtml;
        } else {
            return parent::getBody();
        }
    }

    public function setFrom($fromAddress, $name = null)
    {
        if ($this->mandrillHelper->isMandrillEnabled()) {
            $this->mandrillFrom      = $fromAddress;
            $this->_fromName = $name;
        } else {
            parent::setFrom($fromAddress, $name);
        }

        return $this;
    }

    public function getFromName()
    {
        return $this->_fromName;
    }

    public function getFrom()
    {
        if ($this->mandrillHelper->isMandrillEnabled()) {
            return $this->mandrillFrom;
        } else {
            return parent::getFrom();
        }
    }

    public function getType()
    {
        if ($this->mandrillHelper->isMandrillEnabled()) {
            return $this->mandrillMessageType;
        } else {
            return parent::getType();
        }
    }

    public function getTo()
    {
        return $this->mandrillTo;
    }

    public function getBcc()
    {
        return $this->bcc;
    }

    public function addTo($toAddress, $name = null)
    {
        if ($this->mandrillHelper->isMandrillEnabled()) {
            $this->addEmailAddressToAddresses($toAddress);
        } else {
            parent::addTo($toAddress, $name);
        }

        return $this;
    }

    public function addCc($ccAddress, $name = null)
    {
        if ($this->mandrillHelper->isMandrillEnabled()) {
            $this->addEmailCcAddressToAddresses($ccAddress);
        } else {
            parent::addCc($ccAddress, $name);
        }

        return $this;
    }

    public function addBcc($bccAddress, $name = null)
    {
        if ($this->mandrillHelper->isMandrillEnabled()) {
            $this->addEmailBccAddressToAddresses($bccAddress);
        } else {
            parent::addBcc($bccAddress);
        }

        return $this;
    }

    public function getMessageType()
    {
        return $this->mandrillMessageType;
    }

    public function createAttachment(
        $body,
        $mimeType = \Zend_Mime::TYPE_OCTETSTREAM,
        $disposition = \Zend_Mime::DISPOSITION_ATTACHMENT,
        $encoding = \Zend_Mime::ENCODING_BASE64,
        $filename = null
    ) {
        if ($this->mandrillHelper->isMandrillEnabled()) {
            $att = array('type' => $mimeType,'name' => $filename,'content'=> base64_encode($body));
            array_push($this->att, $att);
        } else {
            parent::createAttachment($body, $mimeType, $disposition, $encoding, $filename);
        }

        return $this;
    }

    public function getAttachments()
    {
        return $this->att;
    }

    public function addHeader($name, $value, $append = false)
    {
        if ($this->mandrillHelper->isMandrillEnabled()) {
            $this->addHeaderToHeaders($name, $value);
        } else {
            parent::addHeader($name, $value, $append);
        }

        return $this;
    }

    public function getHeaders()
    {
        if ($this->mandrillHelper->isMandrillEnabled()) {
            return $this->mandrillHeaders;
        } else {
            return parent::getHeaders();
        }
    }

    public function setReplyTo($email, $name = null)
    {
        if ($this->mandrillHelper->isMandrillEnabled()) {
            $this->addReplyToHeaderToHeaders($email, $name);
        } else {
            return parent::setReplyTo($email, $name = null);
        }

        return $this;
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

    /**
     * @param $toAddress
     */
    private function addEmailAddressToAddresses($toAddress)
    {
        if (is_array($toAddress)) {
            foreach ($toAddress as $address) {
                array_push($this->mandrillTo, $address);
            }
        } else {
            array_push($this->mandrillTo, $toAddress);
        }
    }

    /**
     * @param $ccAddress
     */
    private function addEmailCcAddressToAddresses($ccAddress)
    {
        if (is_array($ccAddress)) {
            foreach ($ccAddress as $address) {
                array_push($this->mandrillTo, $address);
            }
        } else {
            array_push($this->mandrillTo, $ccAddress);
        }
    }

    /**
     * @param $bccAddress
     */
    private function addEmailBccAddressToAddresses($bccAddress)
    {
        if (is_array($bccAddress)) {
            foreach ($bccAddress as $address) {
                array_push($this->bcc, $address);
            }
        } else {
            array_push($this->bcc, $bccAddress);
        }
    }

    /**
     * @param $name
     * @param $value
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addHeaderToHeaders($name, $value)
    {
        $prohibit = array(
            'to',
            'cc',
            'bcc',
            'from',
            'subject',
            'reply-to',
            'return-path',
            'date',
            'message-id',
        );
        if (in_array(strtolower($name), $prohibit)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Cannot set standard header from addHeader()'));
        }

        $this->mandrillHeaders[$name] = $value;
    }

    /**
     * @param $email
     * @param $name
     */
    private function addReplyToHeaderToHeaders($email, $name)
    {
        $email                     = $this->_filterEmail($email);
        $name                      = $this->_filterName($name);
        $this->mandrillHeaders['Reply-To'] = sprintf('%s <%s>', $name, $email);
    }
}
