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

use Magento\Framework\Exception\MailException;
use Psr\Log\LoggerInterface;

class Transport implements \Magento\Framework\Mail\TransportInterface
{
    /**
     * @var \Ebizmarts\Mandrill\Model\Message
     */
    private $message;

    /**
     * @var Api\Mandrill
     */
    private $api;

    /** @var LoggerInterface\ */
    private $logger;

    /**
     * @param \Magento\Framework\Mail\MessageInterface $message
     * @param \Psr\Log\LoggerInterface                 $logger
     * @param \Ebizmarts\Mandrill\Helper\Data          $helper
     */
    public function __construct(
        \Ebizmarts\Mandrill\Model\Message $message,
        \Ebizmarts\Mandrill\Model\Api\Mandrill $api,
        LoggerInterface $logger
    ) {

        $this->message = $message;
        $this->api     = $api;
        $this->logger  = $logger;
    }
    public function sendMessage()
    {
        $mandrillApiInstance = $this->getMandrillApiInstance();

        if ($mandrillApiInstance === null) {
            return false;
        }

        $message    = array(
            'subject'    => $this->message->getSubject(),
            'from_name'  => $this->message->getFromName(),
            'from_email' => $this->message->getFrom(),
        );
        foreach ($this->message->getTo() as $to) {
            $message['to'][] = array(
                'email' => $to
            );
        }
        foreach ($this->message->getBcc() as $bcc) {
            $message['to'][] = array(
                'email' => $bcc,
                'type'  => 'bcc'
            );
        }
        if ($att = $this->message->getAttachments()) {
            $message['attachments'] = $att;
        }
        if ($headers = $this->message->getHeaders()) {
            $message['headers'] = $headers;
        }
        switch ($this->message->getType()) {
            case \Magento\Framework\Mail\MessageInterface::TYPE_HTML:
                $message['html'] = $this->message->getBody();
                break;
            case \Magento\Framework\Mail\MessageInterface::TYPE_TEXT:
                $message['text'] = $this->message->getBody();
                break;
        }

        $result = $mandrillApiInstance->messages->send($message);

        try {
            $this->processApiCallResult($result);
        } catch (MailException $e) {
            $this->logger->error(
                sprintf(
                    'We got "rejected" status from Mandrill for email with subject "%s". ' .
                    'Please review the customer email %s, and process it again. Error details: %s',
                    $this->message->getSubject(),
                    implode(', ', $this->message->getTo()),
                    (string)$e
                )
            );
        }

        return true;
    }

    private function processApiCallResult($result)
    {
        $currentResult = current($result);

        if (array_key_exists('status', $currentResult) && $currentResult['status'] == 'rejected') {
            throw new MailException(
                new \Magento\Framework\Phrase("Email sending failed: %1", [$currentResult['reject_reason']])
            );
        }
    }

    /**
     * @return \Mandrill
     */
    private function getMandrillApiInstance()
    {
        return $this->api->getApi();
    }

    /**
     * Get message
     *
     * @return \Magento\Framework\Mail\MessageInterface
     * @since 100.2.0
     */
    public function getMessage()
    {
        return $this->message;
    }
}
