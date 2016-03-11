<?php
/**
 * Mandrill Magento Component
 *
 * @category Ebizmarts
 * @package Mandrill
 * @author Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date: 2/26/16 2:42 PM
 * @file: DetailsTest.php
 */
namespace Ebizmarts\Mandrill\Test\Unit\Model\Config\Source;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class DetailsTest extends \PHPUnit_Framework_TestCase
{
    protected $_details;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
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
        $usersMock = $this->getMockBuilder('Mandrill\Users')
            ->disableOriginalConstructor()
            ->disableAutoload()
            ->setMethods(array('info'))
            ->getMock();
        $usersMock->expects($this->any())->method('info')->willReturn(['username'=>'gonzalo','reputation'=>1,'hourly_quota'=>10,'backlog'=>0],['account']);
        $mandrillMock->users = $usersMock;
        $this->_details = $objectManager->getObject('Ebizmarts\Mandrill\Model\Config\Source\Details',['helper'=>$helperMock,'api'=>$apiMock]);
    }

    /**
     * @covers Ebizmarts\Mandrill\Model\Config\Source\Details::toOptionArray
     */
    public function testToOptionArray()
    {
        $r = $this->_details->toOptionArray();
        $this->assertEquals([['label'=>'User Name','value'=>'gonzalo'],['label'=>'Reputation','value'=>1],['label'=>'Hourly Quota','value'=>10],['label'=>'Backlog','value'=>0]],$r);
    }
}