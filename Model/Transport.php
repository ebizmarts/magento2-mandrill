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

use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\ResourceModel\Order\Shipment as ShipmentResource;
use Magento\Sales\Model\ResourceModel\Order\Invoice as InvoiceResource;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo as CreditmemoResource;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;

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

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $sendCallResult;

    /**
     * @return array
     */
    public function getSendCallResult()
    {
        return $this->sendCallResult;
    }

    /**
     * @param array $sendCallResult
     */
    public function setSendCallResult($sendCallResult)
    {
        $this->sendCallResult = $sendCallResult;
    }

    /**
     * @var array | Exceptions to be catched to avoid repeated sending which affects reputation.
     */
    private $exceptionArray = [
        "hard-bounce" => true,
        "soft-bounce" => false,
        "spam" => true,
        "unsub" => true,
        "custom" => true,
        "invalid-sender" => true,
        "invalid" => false,
        "test-mode-limit" => false,
        "unsigned" => false,
        "rule" => true
    ];

    // Different type of emails that may be sent.
    const SHIPMENT = "shipment";
    const INVOICE = "invoice";
    const CREDITMEMO = "creditmemo";
    const ORDER = "order";
    const COMMENT = "comment";

    /**
     * List of document types that require email sending.
     */
    const EMAIL_DOCUMENT_TYPES_ARRAY = [
        self::SHIPMENT,
        self::INVOICE,
        self::CREDITMEMO,
        self::ORDER
    ];

    /**
     * Transport constructor.
     * @param Message $message
     * @param Api\Mandrill $api
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Ebizmarts\Mandrill\Model\Message $message,
        \Ebizmarts\Mandrill\Model\Api\Mandrill $api,
        ObjectManagerInterface $objectManager
    )
    {
        $this->message = $message;
        $this->api = $api;
        $this->objectManager = $objectManager;
    }

    /**
     * @return bool|void
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendMessage()
    {
        $mandrillApiInstance = $this->getMandrillApiInstance();

        if ($mandrillApiInstance === null) {
            return false;
        }

        $message = array(
            'subject' => $this->message->getSubject(),
            'from_name' => $this->message->getFromName(),
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
                'type' => 'bcc'
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
        $this->setSendCallResult(current($result));

        $this->processApiCallResult();

        return true;
    }

    /**
     * @throws \Magento\Framework\Exception\MailException
     */
    private function processApiCallResult()
    {
        if ($this->rejectReasonKeyExistsInResult()) {
            if ($this->rejectReasonShouldBeCatched()) {
                $this->updateSendEmailFlag();
                $this->throwMailException();
            }
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

    /**
     * Set send_email flag to null for the correct resource (invoice, shipment or creditmemo).
     *
     * @throws \Magento\Framework\Exception\MailException
     */
    private function updateSendEmailFlag()
    {
        list($resource, $object) = $this->getResourceAndObject();
        $object->setSendEmail(null);
        $object->setEmailSent(null);
        $resource->saveAttribute($object, ['send_email', 'email_sent']);
    }

    /**
     * @param $currentResult
     * @throws \Magento\Framework\Exception\MailException
     */
    private function throwMailException()
    {
        $currentResult = $this->getSendCallResult();
        $email = (array_key_exists('email', $currentResult)) ? $currentResult['email'] : '';
        $rejectReason = (array_key_exists('reject_reason', $currentResult)) ? $currentResult['reject_reason'] : '';
        if (array_key_exists('email', $currentResult) && array_key_exists('reject_reason', $currentResult)) {
            $phrase = new \Magento\Framework\Phrase("Email sending for %1 was rejected. Reason: %2. Goto https://mandrillapp.com/activity for more information.", [$email, $rejectReason]);
        } else {
            $phrase = new \Magento\Framework\Phrase("Error sending email. Goto https://mandrillapp.com/activity for more information.");
        }
        throw new \Magento\Framework\Exception\MailException($phrase);
    }

    /**
     * @param $currentResult
     * @return bool
     */
    private function rejectReasonKeyExistsInResult()
    {
        $currentResult = $this->getSendCallResult();
        return array_key_exists('status', $currentResult) && $currentResult['status'] == 'rejected' && array_key_exists('reject_reason', $currentResult);
    }

    /**
     * @param $currentResult
     * @return bool
     */
    private function rejectReasonShouldBeCatched()
    {
        $currentResult = $this->getSendCallResult();
        return $this->exceptionArray[$currentResult['reject_reason']] === true;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\MailException
     */
    private function getResourceAndObject()
    {
        $templateVars = $this->message->getTemplateContainer()->getTemplateVars();
        $currentDocumentType = $this->getCurrentEmailDocumentType($templateVars);

        switch ($currentDocumentType) {
            case self::SHIPMENT:
                $resource = $this->objectManager->create(ShipmentResource::class);
                $object = $templateVars[self::SHIPMENT];
                break;
            case self::INVOICE:
                $resource = $this->objectManager->create(InvoiceResource::class);
                $object = $templateVars[self::INVOICE];
                break;
            case self::CREDITMEMO:
                $resource = $this->objectManager->create(CreditmemoResource::class);
                $object = $templateVars[self::CREDITMEMO];
                break;
            case self::ORDER:
                $resource = $this->objectManager->create(OrderResource::class);
                $object = $templateVars[self::ORDER];
                break;
            default:
                $this->throwMailException();
                break;
        }
        return array($resource, $object);
    }

    /**
     * @param $templateVars
     * @return null
     */
    private function getCurrentEmailDocumentType($templateVars)
    {
        $currentDocumentType = null;
        $varIds = array_keys($templateVars);
        foreach (self::EMAIL_DOCUMENT_TYPES_ARRAY as $posibleDocumentType) {
            if ($this->isRealDocumentType($posibleDocumentType, $varIds, $currentDocumentType)) {
                    $currentDocumentType = $posibleDocumentType;
            }
        }
        return $currentDocumentType;
    }

    /**
     * @param $posibleDocumentType
     * @param $varIds
     * @param $currentDocumentType
     * @return bool
     */
    private function isRealDocumentType($posibleDocumentType, $varIds, $currentDocumentType)
    {
        $isOneOfExpectedValues = in_array($posibleDocumentType, $varIds);
        $typeNotFoundAlready = $currentDocumentType === null;

        //Order type exists in all the emails, should skip it unless it is the last one
        $isNotOrder = $posibleDocumentType != self::ORDER;

        //When order is found, make sure there is not comment within the templateVars to avoid comment emails.
        $isNotComment = !in_array(self::COMMENT, $varIds);

        return $isOneOfExpectedValues && $typeNotFoundAlready && $isNotOrder || $isNotComment);
    }
}
