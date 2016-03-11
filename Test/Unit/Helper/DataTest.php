<?php
/**
 * Mandrill Magento Component
 *
 * @category Ebizmarts
 * @package Mandrill
 * @author Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date: 2/22/16 5:22 PM
 * @file: DataTest.php
 */
namespace Ebizmarts\Mandrill\Test\Unit\Helper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class DataTest extends \PHPUnit_Framework_TestCase
{
    protected $_scopeMock;
    protected $_helper;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->_scopeMock = $this->getMockBuilder('Magento\Framework\App\Config\ScopeConfigInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock = $this->getMockBuilder('Magento\Framework\App\Helper\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $contextMock->expects($this->any())
            ->method('getScopeConfig')
            ->willReturn($this->_scopeMock);
        $this->_helper = $objectManager->getObject('Ebizmarts\Mandrill\Helper\Data',['context'=>$contextMock]);
    }

    /**
     * @covers Ebizmarts\Mandrill\Helper\Data::getApiKey
     * @covers Ebizmarts\Mandrill\Helper\Data::getTestSender
     */
    public function testGetApiKey()
    {
        $this->_scopeMock->expects($this->once())
            ->method('getValue')
            ->willReturn('vt48WV1AdLz5kzNDr2JwnQ');
        $this->assertEquals($this->_helper->getApiKey(),'vt48WV1AdLz5kzNDr2JwnQ');
    }

    /**
     * @covers Ebizmarts\Mandrill\Helper\Data::isActive
     */
    public function testIsActive()
    {
        $this->_scopeMock->expects($this->once())
            ->method('getValue')
            ->willReturn('yes');
        $this->assertEquals($this->_helper->isActive(),'yes');
    }
    /**
     * @covers Ebizmarts\Mandrill\Helper\Data::getTestSender
     */
    public function testGetTestSender()
    {
        $this->_scopeMock->expects($this->once())
            ->method('getValue')
            ->willReturn('gonzalo');
        $this->assertEquals($this->_helper->getTestSender(),'gonzalo');
    }
}