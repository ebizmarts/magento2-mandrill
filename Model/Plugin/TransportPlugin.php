<?php

namespace Ebizmarts\Mandrill\Model\Plugin;

class TransportPlugin
{
    private $mandrillTransportFactory;

    private $mandrillHelper;

    public function __construct(
        \Ebizmarts\Mandrill\Helper\Data $mandrillHelper,
        \Ebizmarts\Mandrill\Model\TransportFactory $mandrillTransportFactory
    ) {
        $this->mandrillHelper = $mandrillHelper;
        $this->mandrillTransportFactory = $mandrillTransportFactory;
    }

    public function aroundSendMessage(\Magento\Framework\Mail\Transport $mailTransport, callable $proceed)
    {
        if ($this->isMandrillEnabled() === false) {
            $proceed();
        } else {
            /** @var \Ebizmarts\Mandrill\Model\Transport $mandrillTransport */
            $mandrillTransport = $this->mandrillTransportFactory->create();
            $mandrillTransport->sendMessage();
        }

        return null;
    }

    /**
     * @return bool
     */
    private function isMandrillEnabled()
    {
        return $this->mandrillHelper->isMandrillEnabled();
    }
}
