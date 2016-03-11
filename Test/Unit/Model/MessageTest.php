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
        $helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $helperMock = $this->getMockBuilder('Ebizmarts\Mandrill\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $helperMock->expects($this->any())->method('getApiKey')->willReturn('vt48WV1AdLz5kzNDr2JwnQ');
        $apiMock = $this->getMockBuilder('Ebizmarts\Mandrill\Model\Api\Mandrill')
            ->disableOriginalConstructor()
            ->getMock();
        $mandrillMock = $this->getMockBuilder('Mandrill')
            ->disableOriginalConstructor()
            ->getMock();
        $apiMock->expects($this->any())->method('getApi')->willReturn($mandrillMock);
        $messagesMock = $this->getMockBuilder('Mandrill\Messages')
            ->disableOriginalConstructor()
            ->disableAutoload()
            ->setMethods(array('send'))
            ->getMock();
        $messagesMock->expects($this->any())->method('send')->willReturn(true);
        $mandrillMock->messages = $messagesMock;

        $this->_message = $helper->getObject('Ebizmarts\Mandrill\Model\Message',['helper'=>$helperMock,'api'=>$apiMock]);
    }

    /**
     * @covers Ebizmarts\Mandrill\Model\Message::setSubject
     * @covers Ebizmarts\Mandrill\Model\Message::getSubject
     */
    public function testSetSubject()
    {
        $this->_message->setSubject('subject');
        $this->assertEquals('subject',$this->_message->getSubject());
    }
    /**
     * @covers Ebizmarts\Mandrill\Model\Message::setBody
     * @covers Ebizmarts\Mandrill\Model\Message::getBody
     */
    public function testSetBody()
    {
        $this->_message->setBody('body');
        $this->assertEquals('body',$this->_message->getBody());
    }
    /**
     * @covers Ebizmarts\Mandrill\Model\Message::setFrom
     * @covers Ebizmarts\Mandrill\Model\Message::getFrom
     */
    public function testSetFrom()
    {
        $this->_message->setFrom('from');
        $this->assertEquals('from',$this->_message->getFrom());
    }
    /**
     * @covers Ebizmarts\Mandrill\Model\Message::addTo
     * @covers Ebizmarts\Mandrill\Model\Message::getTo
     */
    public function testAddTo()
    {
        $this->_message->addTo('to');
        $this->assertEquals(array('to'),$this->_message->getTo());
        $this->_message->addTo(array('to1','to2'));
        $this->assertEquals(array('to','to1','to2'),$this->_message->getTo());
    }
    /**
     * @covers Ebizmarts\Mandrill\Model\Message::addCc
     */
    public function testAddCc()
    {
        $this->_message->addCc('cc');
        $this->assertEquals(array('cc'),$this->_message->getTo());
        $this->_message->addCc(array('cc1','cc2'));
        $this->assertEquals(array('cc','cc1','cc2'),$this->_message->getTo());
    }
    /**
     * @covers Ebizmarts\Mandrill\Model\Message::addBcc
     * @covers Ebizmarts\Mandrill\Model\Message::getBcc
     */
    public function testAddBcc()
    {
        $this->_message->addBcc('bcc');
        $this->assertEquals(array('bcc'),$this->_message->getBcc());
        $this->_message->addBcc(array('bcc1','bcc2'));
        $this->assertEquals(array('bcc','bcc1','bcc2'),$this->_message->getBcc());
    }
    /**
     * @covers Ebizmarts\Mandrill\Model\Message::setMessageType
     * @covers Ebizmarts\Mandrill\Model\Message::getMessageType
     * @covers Ebizmarts\Mandrill\Model\Message::getType
     */
    public function testSetMessageType()
    {
        $this->_message->setMessageType('mt');
        $this->assertEquals('mt',$this->_message->getMessageType());
        $this->assertEquals('mt',$this->_message->getType());
    }
    /**
     * @covers Ebizmarts\Mandrill\Model\Message::createAttachment
     * @covers Ebizmarts\Mandrill\Model\Message::getAttachments
     */
    public function testCreateAttachment()
    {
        $this->_message->createAttachment('body',\Zend_Mime::TYPE_OCTETSTREAM,\Zend_Mime::DISPOSITION_ATTACHMENT,\Zend_Mime::ENCODING_BASE64,'filename');
        $att = $this->_message->getAttachments();
//        $this->assertEquals('body',base64_decode($att[0]['content']));
        $this->assertEquals('filename',$att[0]['name']);
        $this->assertEquals(\Zend_Mime::TYPE_OCTETSTREAM,$att[0]['type']);
    }
    /**
     * @covers Ebizmarts\Mandrill\Model\Message::addHeader
     * @covers Ebizmarts\Mandrill\Model\Message::getHeaders
     */
    public function testAddHeader()
    {
        $this->_message->addHeader('header','value');
        $h = $this->_message->getHeaders();
        $this->assertEquals('value',$h['header']);
    }

    /**
     * @covers Ebizmarts\Mandrill\Model\Message::addHeader
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Cannot set standard header from addHeader()
     */
    public function testAddHeaderWithException()
    {
        $this->_message->addHeader('to','value');
    }
    /**
     * @covers Ebizmarts\Mandrill\Model\Message::setReplyTo
     * @covers Ebizmarts\Mandrill\Model\Message::getMessageType
     */
    public function testSetReplyTo()
    {
        $this->_message->setReplyTo("info@ebizmarts.com","ebizmarts");
        $h = $this->_message->getHeaders();
        $this->assertEquals($h[0]['Reply-To'],'ebizmarts <info@ebizmarts.com>');
        $this->_message->setReplyTo("info@ebizmarts.com\n\t<","<ebizmarts>");
        $h = $this->_message->getHeaders();
        $this->assertEquals($h[0]['Reply-To'],'ebizmarts <info@ebizmarts.com>');
    }
    public function testSend()
    {
        $this->_message->setFrom('gonzalo@ebizmarts.com','gonzalo');
        $this->_message->addTo('gonzalo@ebizmarts.com');
        $this->_message->addBcc('gonzalo2@ebizmarts.com');
        $this->_message->setReplyTo("gonzalo");
        $this->_message->createAttachment("test att");
        $this->_message->setBody('body');
        $this->_message->send();
        $this->_message->setMessageType(\Magento\Framework\Mail\MessageInterface::TYPE_HTML);
        $this->_message->setBody('body');
        $this->_message->send();
    }
}