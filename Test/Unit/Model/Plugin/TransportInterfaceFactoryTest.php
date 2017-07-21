<?php

namespace Ebizmarts\Mandrill\Test\Unit\Model\Plugin;

class TransportInterfaceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Main TransportInterfaceFactory test class
     *
     * @var \Ebizmarts\Mandrill\Model\Plugin\TransportInterfaceFactory
     */
    protected $factoryClass;

    /**
     * Returned Mandrill transport model
     *
     * @var \Ebizmarts\Mandrill\Model\Transport
     */
    protected $mandrillTransportMock;

    /**
     * Constructor message data
     *
     * @var array
     */
    protected $data;

    public function setUp()
    {
        $mailMessageMock = $this->getMockBuilder(\Magento\Framework\Mail\Message::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->data = [
            'message' => $mailMessageMock
        ];

        $mandrillHelperMock = $this->getMockBuilder(\Ebizmarts\Mandrill\Helper\Data::class)
            ->setMethods(['isMandrillEnabled'])
            ->disableOriginalConstructor()
            ->getMock();
        $mandrillHelperMock->expects($this->at(0))->method('isMandrillEnabled')->will($this->returnValue(false));
        $mandrillHelperMock->expects($this->at(1))->method('isMandrillEnabled')->will($this->returnValue(true));

        $mandrillTransportFactoryMock = $this->getMockBuilder(\Ebizmarts\Mandrill\Model\TransportFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->mandrillTransportMock = $this->getMockBuilder(\Ebizmarts\Mandrill\Model\Transport::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mandrillTransportFactoryMock->expects($this->once())->method('create')
            ->with($this->equalTo($this->data))->willReturn($this->mandrillTransportMock);

        $this->factoryClass = new \Ebizmarts\Mandrill\Model\Plugin\TransportInterfaceFactory($mandrillHelperMock, $mandrillTransportFactoryMock);
    }

    public function testAroundCreate()
    {
        $transportFactoryMock = $this->getMockBuilder(\Magento\Framework\Mail\TransportInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $transportInterfaceMock = $this->getMockBuilder(\Magento\Framework\Mail\TrasportInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $closure = function ($data) use ($transportInterfaceMock) {
            $this->assertSame($this->data, $data);
            return $transportInterfaceMock;
        };

        $this->assertEquals(
            $transportInterfaceMock,
            $this->factoryClass->aroundCreate($transportFactoryMock, $closure, $this->data)
        );

        $this->assertEquals(
            $this->mandrillTransportMock,
            $this->factoryClass->aroundCreate($transportFactoryMock, $closure, $this->data)
        );
    }
}
