<?php

namespace Ebizmarts\Mandrill\Test\Unit\Model\Plugin;

class TransportPluginTest extends \PHPUnit_Framework_TestCase
{
    public function testAroundSaveNotProceed()
    {
        $mailTransportMock = $this->getMockBuilder(\Magento\Framework\Mail\Transport::class)
            ->disableOriginalConstructor()
            ->getMock();

        $callable = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $callable->expects($this->once())->method('__invoke');

        $mandrillHelperMock = $this->getMockBuilder(\Ebizmarts\Mandrill\Helper\Data::class)
            ->setMethods(['isActive'])
            ->disableOriginalConstructor()
            ->getMock();
        $mandrillHelperMock->expects($this->once())->method('isActive')->willReturn(0);

        $mandrillTransportFactoryMock = $this->getMockBuilder(\Ebizmarts\Mandrill\Model\TransportFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mandrillTransportFactoryMock->expects($this->never())->method('sendMessage');

        $this->callAroundMessage($mandrillHelperMock, $mandrillTransportFactoryMock, $mailTransportMock, $callable);
    }

    public function testAroundSaveMandrill()
    {
        $mailTransportMock = $this->getMockBuilder(\Magento\Framework\Mail\Transport::class)
            ->disableOriginalConstructor()
            ->getMock();

        $callable = $this->getMockBuilder(\stdClass::class)->setMethods(['__invoke'])->getMock();
        $callable->expects($this->never())->method('__invoke');

        $mandrillHelperMock = $this->getMockBuilder(\Ebizmarts\Mandrill\Helper\Data::class)
            ->setMethods(['isActive'])
            ->disableOriginalConstructor()
            ->getMock();
        $mandrillHelperMock->expects($this->once())->method('isActive')->willReturn(1);

        $mandrillTransportMock = $this->getMockBuilder(\Ebizmarts\Mandrill\Model\Transport::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendMessage'])
            ->getMock();
        $mandrillTransportMock->expects($this->once())->method('sendMessage');

        $mandrillTransportFactoryMock = $this->getMockBuilder(\Ebizmarts\Mandrill\Model\TransportFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $mandrillTransportFactoryMock->expects($this->once())->method('create')->willReturn($mandrillTransportMock);

        $this->callAroundMessage($mandrillHelperMock, $mandrillTransportFactoryMock, $mailTransportMock, $callable);
    }

    /**
     * @param $mandrillHelperMock
     * @param $mandrillTransportFactoryMock
     * @param $mailTransportMock
     * @param $callable
     */
    private function callAroundMessage(
        $mandrillHelperMock,
        $mandrillTransportFactoryMock,
        $mailTransportMock,
        $callable
    ) {
        $objectManager   = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $transportPlugin = $objectManager->getObject("Ebizmarts\Mandrill\Model\Plugin\TransportPlugin", [
                "mandrillHelper"           => $mandrillHelperMock,
                "mandrillTransportFactory" => $mandrillTransportFactoryMock
            ]);
        $transportPlugin->aroundSendMessage($mailTransportMock, $callable);
    }
}
