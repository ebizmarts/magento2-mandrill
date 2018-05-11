<?php
namespace Ebizmarts\Mandrill\Model;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\Template\TransportBuilderByStore;
use Magento\Sales\Model\Order\Email\Container\IdentityInterface;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\ObjectManagerInterface;

class SenderBuilder extends \Magento\Sales\Model\Order\Email\SenderBuilder
{
    /**
     * SenderBuilder constructor.
     *
     * @param Template $templateContainer
     * @param IdentityInterface $identityContainer
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Template $templateContainer,
        IdentityInterface $identityContainer,
        ObjectManagerInterface $objectManager
    ) {
        /** @var MessageInterface $message */
        $message = $objectManager->create(MessageInterface::class);
        /** @var TransportBuilder $transportBuilder */
        $transportBuilder = $objectManager->create(
            TransportBuilder::class,
            ["message" => $message]
        );
        /** @var TransportBuilderByStore $transportBuilderByStore */
        $transportBuilderByStore = $objectManager->create(
            TransportBuilderByStore::class,
            ["message" => $message]
        );
        parent::__construct($templateContainer, $identityContainer, $transportBuilder, $transportBuilderByStore);
    }
}
