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
        $this->_message = $helper->getObject('Ebizmarts\Mandrill\Model\Message');
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
    }
    /**
     * @covers Ebizmarts\Mandrill\Model\Message::addCc
     */
    public function testAddCc()
    {
        $this->_message->addCc('cc');
        $this->assertEquals(array('cc'),$this->_message->getTo());
    }
    /**
     * @covers Ebizmarts\Mandrill\Model\Message::addBcc
     * @covers Ebizmarts\Mandrill\Model\Message::getBcc
     */
    public function testAddBcc()
    {
        $this->_message->addBcc('bcc');
        $this->assertEquals(array('bcc'),$this->_message->getBcc());
    }
    /**
     * @covers Ebizmarts\Mandrill\Model\Message::setMessageType
     * @covers Ebizmarts\Mandrill\Model\Message::getMessageType
     */
    public function testSetMessageType()
    {
        $this->_message->setMessageType('mt');
        $this->assertEquals('mt',$this->_message->getMessageType());
    }
    /**
     * @covers Ebizmarts\Mandrill\Model\Message::createAttachment
     * @covers Ebizmarts\Mandrill\Model\Message::getAttachments
     */
    public function testCreateAttachment()
    {
        $this->_message->createAttachment('body',\Zend_Mime::TYPE_OCTETSTREAM,\Zend_Mime::DISPOSITION_ATTACHMENT,\Zend_Mime::ENCODING_BASE64,'filename');
        $att = $this->_message->getAttachments();
        $this->assertEquals('body',base64_decode($att[0]['content']));
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
        echo "missing ".__METHOD__."\n";
    }
}