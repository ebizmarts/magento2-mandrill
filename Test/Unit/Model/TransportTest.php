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
        $objectManager = new ObjectManager($this);
        $this->_message = $objectManager->getObject('Ebizmarts\Mandrill\Model\Message');
        $helper = $this->getMockBuilder('Ebizmarts\Mandrill\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $helper->expects($this->once())->method('getApiKey')->willReturn('vt48WV1AdLz5kzNDr2JwnQ');
        $this->_transport = $objectManager->getObject('Ebizmarts\Mandrill\Model\Transport',['message'=> $this->_message, 'helper'=>$helper]);
    }

    /**
     * @covers Ebizmarts\Mandrill\Model\Transport::sendMessage
     */
    public function testSendMessage()
    {
        $this->_transport->sendMessage();
    }
}
