<?php
/**
 * Mandrill Magento Component
 *
 * @category Ebizmarts
 * @package Mandrill
 * @author Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date: 2/12/16 3:32 PM
 * @file: TransportTest.php
 */
namespace Ebizmarts\Mandrill\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use \Ebizmarts\Mandrill\Model\Transport;

class TransportTest extends \PHPUnit_Framework_TestCase
{
    private $mandrillApiMock;
    private $objectManager;
    private $messagesMock;
    private $helperMock;
    private $apiMock;
    /**
     * @var \Ebizmarts\Mandrill\Model\Transport
     */
    protected $_transport;
    /**
     * @var \Ebizmarts\Mandrill\Model\Message
     */
    protected $_message;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->mandrillApiMock = $this->getMockBuilder('Mandrill')->disableOriginalConstructor()->getMock();

        $this->_message = $this->objectManager->getObject('Ebizmarts\Mandrill\Model\Message');
        $this->helperMock = $this->getMockBuilder('Ebizmarts\Mandrill\Helper\Data')->disableOriginalConstructor()->getMock();
        $this->helperMock->expects($this->any())->method('getApiKey')->willReturn('vt48WV1AdLz5kzNDr2JwnQ');
        $this->apiMock = $this->getMockBuilder('Ebizmarts\Mandrill\Model\Api\Mandrill')
            ->disableOriginalConstructor()
            ->getMock();

        $this->messagesMock = $this->getMockBuilder('Mandrill\Messages')->disableOriginalConstructor()->disableAutoload()->setMethods(array('send'))->getMock();
        $this->messagesMock->expects($this->any())->method('send')->willReturn(
            [
                [
                    'status' => 'accepted',
                    'email' => 'gonzalo@ebizmarts.com',
                    '_id' => 'da911aasd132',
                    'reject_reason' => ''
                ]
            ]
        );
    }

    /**
     * @covers \Ebizmarts\Mandrill\Model\Transport::sendMessage
     */
    public function testSendMessage()
    {
        $this->apiMock->expects($this->any())->method('getApi')->willReturn($this->mandrillApiMock);

        $this->mandrillApiMock->messages = $this->messagesMock;
        $this->_transport                = $this->objectManager
            ->getObject('Ebizmarts\Mandrill\Model\Transport', [
                'message'=> $this->_message,
                'helper'=> $this->helperMock,
                'api'=>$this->apiMock
                ]);

        $this->_message->addTo('gonzalo@ebizmarts.com');
        $this->_message->addBcc('gonzalo2@ebizmarts.com');
        $this->_message->setReplyTo("gonzalo");
        $this->_message->createAttachment("test att");
        $this->assertEquals(true, $this->_transport->sendMessage());
        $this->_message->setMessageType(\Magento\Framework\Mail\MessageInterface::TYPE_HTML);
        $this->assertEquals(true, $this->_transport->sendMessage());
    }

    /**
     * @covers \Ebizmarts\Mandrill\Model\Transport::sendMessage
     */
    public function testSendMessageDisabled()
    {
        $this->mandrillApiMock = null;
        $this->apiMock->expects($this->any())->method('getApi')->willReturn($this->mandrillApiMock);

        $this->_transport = $this->objectManager
            ->getObject('Ebizmarts\Mandrill\Model\Transport', [
                'message'=> $this->_message,
                'helper'=> $this->helperMock,
                'api'=>$this->apiMock
                ]);

        $this->assertEquals(false, $this->_transport->sendMessage());
    }
}
