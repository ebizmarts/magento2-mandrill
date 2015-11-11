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