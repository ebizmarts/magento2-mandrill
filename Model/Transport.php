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
        $message = $this->getMessage();
        $mandrillApiInstance = $this->getMandrillApiInstance();

        if ($mandrillApiInstance === null) {
            return false;
        }

        $messageData = array(
            'subject' => $message->getSubject(),
            'from_name' => $message->getFromName(),
            'from_email' => $message->getFrom(),
        );
        foreach ($message->getTo() as $to) {
            $messageData['to'][] = array(
                'email' => $to
            );
        }
        foreach ($message->getBcc() as $bcc) {
            $messageData['to'][] = array(
                'email' => $bcc,
                'type' => 'bcc'
            );
        }
        if ($att = $message->getAttachments()) {
            $messageData['attachments'] = $att;
        }
        if ($headers = $message->getHeaders()) {
            $messageData['headers'] = $headers;
        }
        switch ($message->getType()) {
            case \Magento\Framework\Mail\MessageInterface::TYPE_HTML:
                $messageData['html'] = $message->getBody();
                break;
            case \Magento\Framework\Mail\MessageInterface::TYPE_TEXT:
                $messageData['text'] = $message->getBody();
                break;
        }

        $result = $mandrillApiInstance->messages->send($messageData);
        $this->setSendCallResult(current($result));

        $this->processApiCallResult();

        return true;
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
     * @return \Mandrill
     */
    private function getMandrillApiInstance()
    {
        return $this->api->getApi();
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
     * @param $currentResult
     * @return bool
     */
    private function rejectReasonKeyExistsInResult()
    {
        $currentResult = $this->getSendCallResult();
        
        $isStatusAvailable = $this->isStatusAvailable($currentResult);
        $isStatusRejected = $this->isStatusRejected($currentResult);
        
        $isRejectedReasonAvailable = $this->isRejectReasonAvailable($currentResult);
        
        return $isStatusAvailable && $isStatusRejected && $isRejectedReasonAvailable;
    }

    /**
     * @param $currentResult
     * @return bool
     */
    private function isStatusAvailable($currentResult)
    {
        return array_key_exists('status', $currentResult);
    }

    /**
     * @param $currentResult
     * @return bool
     */
    private function isStatusRejected($currentResult)
    {
        return $currentResult['status'] == 'rejected';
    }

    /**
     * @param $currentResult
     * @return bool
     */
    private function isRejectReasonAvailable($currentResult)
    {
        return array_key_exists('reject_reason', $currentResult);
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
     * @return array
     * @throws \Magento\Framework\Exception\MailException
     */
    private function getResourceAndObject()
    {
        $templateVars = $this->getMessage()->getTemplateContainer()->getTemplateVars();
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
        foreach (self::EMAIL_DOCUMENT_TYPES_ARRAY as $posibleDocumentType) {
            
            $isCurrentDocTypeEmpty = $this->isCurrentDocTypeEmpty($currentDocumentType);
            $isActualDocumentType = $this->isActualDocumentType($posibleDocumentType, $templateVars);
            
            if ($isCurrentDocTypeEmpty && $isActualDocumentType) {
                $currentDocumentType = $posibleDocumentType;
            }
        }
        return $currentDocumentType;
    }

    /**
     * @param $currentDocumentType
     * @return bool
     */
    private function isCurrentDocTypeEmpty($currentDocumentType)
    {
        return $currentDocumentType === null;
    }

    /**
     * @param $posibleDocumentType
     * @param $templateVars
     * @return bool
     */
    private function isActualDocumentType($posibleDocumentType, $templateVars)
    {
        $isOneOfExpectedTypes = $this->isOneOfExpectedTypes($posibleDocumentType, $templateVars);

        //Order type exists in all the emails, should skip it unless it is the last one
        $isOrderType = $this->isOrderType($posibleDocumentType);

        //When order is found, make sure there is no comment within the templateVars to avoid comment emails.
        $isComment = $this->isCommentEmail($templateVars);

        return $isOneOfExpectedTypes && (!$isOrderType || !$isComment);
    }

    /**
     * @param $posibleDocumentType
     * @param $templateVars
     * @return bool
     */
    private function isOneOfExpectedTypes($posibleDocumentType, $templateVars)
    {
        $varIds = array_keys($templateVars);
        return in_array($posibleDocumentType, $varIds);
    }

    /**
     * @param $posibleDocumentType
     * @return bool
     */
    private function isOrderType($posibleDocumentType)
    {
        return $posibleDocumentType === self::ORDER;
    }

    /**
     * @param $templateVars
     * @return bool
     */
    private function isCommentEmail($templateVars)
    {
        $varIds = array_keys($templateVars);
        return in_array(self::COMMENT, $varIds);
    }

    /**
     * @param $currentResult
     * @throws \Magento\Framework\Exception\MailException
     */
    private function throwMailException()
    {
        $currentResult = $this->getSendCallResult();
        if ($this->isEmailAvailable($currentResult) && $this->isRejectReasonAvailable($currentResult)) {
            $phrase = new \Magento\Framework\Phrase("Email sending for %1 was rejected. Reason: %2. Goto https://mandrillapp.com/activity for more information.", [$currentResult['email'], $currentResult['reject_reason']]);
        } else {
            $phrase = new \Magento\Framework\Phrase("Error sending email. Goto https://mandrillapp.com/activity for more information.");
        }
        throw new \Magento\Framework\Exception\MailException($phrase);
    }

    /**
     * @param $currentResult
     * @return bool
     */
    private function isEmailAvailable($currentResult)
    {
        return array_key_exists('email', $currentResult);
    }
}
