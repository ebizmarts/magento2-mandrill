<?php
/**
 * Author: info@ebizmarts.com
 * Date: 7/20/15
 * Time: 3:42 PM
 * File: MessageTest.php
 * Module: magento2-mandrill
 */

namespace Ebizmarts\Mandrill\Test\Unit\Model;
use \Ebizmarts\Mandrill\Model\Message;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;



class MessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Ebizmarts\Mandrill\Model\Message
     */
    protected $_message;

    protected function setUp()
    {
        $helper = new ObjectManagerHelper($this);
        $arguments = $helper->getConstructArguments('Ebizmarts\Mandrill\Model\Message',[
            'logger' => $this->getMock('\Psr\Log\LoggerInterface'),
            'helper' => $this->getMock('Ebizmarts\Mandrill\Helper\Data')
        ]);
        $this->_message = $helper->getObject('Ebizmarts\Mandrill\Model\Message',$arguments);
    }

    /**
     * @covers Ebizmarts\Mandrill\Model\Message:setSubject
     * @covers Ebizmarts\Mandrill\Model\Message:getSubject
     */
    public function testSetSubject()
    {
        $this->_message->setSubject('subject');
        $this->assertEquals('subject',$this->_message->getSubject());
    }
}