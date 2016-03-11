<?php
/**
 * Mandrill Magento Component
 *
 * @category Ebizmarts
 * @package Mandrill
 * @author Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date: 2/17/16 2:21 PM
 * @file: TestTest.php
 */
namespace Ebizmarts\Mandrill\Test\Unit\Controller\Adminhtml\Email;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class TestTest extends \PHPUnit_Framework_TestCase
{
    protected $request;
    protected $helper;
    protected $test;
    protected $resultJson;


    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->request = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $this->request->expects($this->any())
            ->method('getParam')
            ->with('email')
            ->will($this->returnValue('gonzalo@ebizmarts.com'));

        $this->resultJson = $this->getMockBuilder('Magento\Framework\Controller\Result\Json')
            ->disableOriginalConstructor()
            ->getMock();
        $resultFactoryMock = $this->getMockBuilder('Magento\Framework\Controller\ResultFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $resultFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->resultJson);

        $context = $this->getMockBuilder('Magento\Backend\App\Action\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $context->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($this->request));

        $context->expects($this->any())
            ->method('getResultFactory')
            ->will($this->returnValue($resultFactoryMock));

        $helper = $this->getMockBuilder('Ebizmarts\Mandrill\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();

        $helper->expects($this->once())->method('getTestSender')->willReturn("info@ebizmarts.com");

        $transportB = $this->getMockBuilder('Magento\Framework\Mail\Template\TransportBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $transport = $this->getMockBuilder('Ebizmarts\Mandrill\Model\Transport')
            ->disableOriginalConstructor()
            ->getMock();
        $transportB->expects($this->once())->method('getTransport')->willReturn($transport);

        $this->test = $objectManager->getObject('Ebizmarts\Mandrill\Controller\Adminhtml\Email\Test',['context'=>$context,'transportBuilder'=>$transportB,'helper'=>$helper]);
    }

    /**
     * @covers Ebizmarts\Mandrill\Controller\Adminhtml\Email\Test::execute
     */
    public function testExecute()
    {
        $this->_expectResultJson([
            "error" => 0
        ]);
        $result = $this->test->execute();
    }
    protected function _expectResultJson($result)
    {
        $this->resultJson->expects($this->once())
            ->method('setData')
            ->with($result);
    }
}