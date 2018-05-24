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


namespace Ebizmarts\Mandrill\Test\Unit\Model;

use Ebizmarts\Mandrill\Model\SenderBuilder;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\Template\TransportBuilderByStore;
use \Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\Order\Email\Container\IdentityInterface;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Framework\Mail\MessageInterface;

class SenderBuilderTest extends \PHPUnit\Framework\TestCase
{
    public function testSameMessage()
    {
        // Core mock class
        $builder = $this->getMockBuilder(SenderBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Mock parameters
        $templateMock = $this->getMockBuilder(Template::class)
            ->disableOriginalConstructor()
            ->getMock();
        $identityMock = $this->getMockBuilder(IdentityInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $objectManagerMock = $this->getMockBuilder(ObjectManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Message object to get and test against
        $messageMock = $this->getMockBuilder(MessageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Transport builders created by ObjectManager
        $transportBuilderMock = $this->getMockBuilder(TransportBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $transportBuilderSenderMock = $this->getMockBuilder(TransportBuilderByStore::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Make sure the ObjectManager returns our mock Message
        $objectManagerMock->expects($this->at(0))
            ->method('create')
            ->will($this->returnValue($messageMock));

        // Tests that both TransportBuilder models are created with the same Message object
        $objectManagerMock->expects($this->at(1))
            ->method('create')
            ->with($this->equalTo(TransportBuilder::class), $this->contains($messageMock))
            ->will($this->returnValue($transportBuilderMock));
        $objectManagerMock->expects($this->at(2))
            ->method('create')
            ->with($this->equalTo(TransportBuilderByStore::class), $this->contains($messageMock))
            ->will($this->returnValue($transportBuilderSenderMock));

        // Create a reflection of the SenderBuilder constructor to invoke on our mock Builder
        $reflection = new \ReflectionClass(SenderBuilder::class);
        $constructor = $reflection->getConstructor();

        // Invoke the mock Builder with a reflection of the constructor and our mock objects
        $constructor->invoke($builder, $templateMock, $identityMock, $objectManagerMock);
    }
}
