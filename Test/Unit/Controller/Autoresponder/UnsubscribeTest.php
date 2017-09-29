<?php

namespace Ebizmarts\Mandrill\Test\Unit\Controller\Autoresponder;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class UnsubscribeTest extends \PHPUnit_Framework_TestCase
{

    public function testExecuteUnsubscribe()
    {
        $objectManager = new ObjectManager($this);

        /** @var \Ebizmarts\Mandrill\Controller\Autoresponder\Unsubscribe $unsubscribeController */
        $unsubscribeController = $objectManager
            ->getObject(
                \Ebizmarts\Mandrill\Controller\Autoresponder\Unsubscribe::class, ["context" => $this->makeContext()]
            );

        $unsubscribeController->execute();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function makeRequestObject()
    {
        $request = $this->getMockBuilder(\Magento\Framework\App\Request\Http::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request
            ->expects($this->exactly(3))
            ->method("getParam")
            ->withConsecutive(
                ["email", false],
                ["list", false],
                ["store", false]
            )
            ->willReturnOnConsecutiveCalls("gonzalo@ebizmarts.com", "1234", "default");

        return $request;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function makeContext()
    {
        $context = $this->getMockBuilder(\Magento\Backend\App\Action\Context::class)->disableOriginalConstructor()->getMock();

        $context->expects($this->exactly(3))->method("getRequest")->willReturn($this->makeRequestObject());
        $context->expects($this->any())->method("getObjectManager")->willReturn($this->makeObjectManagerMock());

        return $context;
    }

    private function makeObjectManagerMock()
    {
        $objectManagerMock = $this->getMockBuilder(\Magento\Framework\ObjectManager\ObjectManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $objectManagerMock->expects($this->once())->method("create")->with("\Ebizmarts\Mandrill\Model\Unsubscribe")->willReturn($this->makeMandrillUnsubsribeMock());

        return $objectManagerMock;
    }

    private function makeMandrillUnsubsribeMock()
    {
        $collectionMock = $this->getMockBuilder(\Ebizmarts\Mandrill\Model\Unsubscribe::class)
            ->setMethods(["getCollection"])
            ->disableOriginalConstructor()
            ->getMock();
        $collectionMock->expects($this->once())->method("getCollection")->willReturn(
            $this->makeMandrillUnsubscribeCollection()
        );

        return $collectionMock;
    }

    private function makeMandrillUnsubscribeCollection()
    {
        $collectionMock = $this->getMockBuilder(\Ebizmarts\Mandrill\Model\ResourceModel\Unsubscribe\Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $collectionMock
            ->expects($this->exactly(3))
            ->method("addFieldToFilter")
            ->withConsecutive(
                ["main_table.email", ["eq" => "gonzalo@ebizmarts.com"]],
                ["main_table.list", ["eq" => "1234"]],
                ["main_table.store_id", ["eq" => "default"]]
            )
            ->willReturnSelf();

        $collectionMock->expects($this->once())->method("setPageSize")->with(1);

        return $collectionMock;
    }

}