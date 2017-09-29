<?php

namespace Ebizmarts\Mandrill\Test\Unit\Controller\Autoresponder;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class UnsubscribeTest extends \PHPUnit_Framework_TestCase
{

    const EMAIL = "gonzalo@ebizmarts.com";
    const LIST = "1234";
    const STORE = "default";

    public function testExecuteUnsubscribe()
    {
        $objectManager = new ObjectManager($this);

        /** @var \Ebizmarts\Mandrill\Controller\Autoresponder\Unsubscribe $unsubscribeController */
        $unsubscribeController = $objectManager
            ->getObject(
                \Ebizmarts\Mandrill\Controller\Autoresponder\Unsubscribe::class,
                ["context" => $this->makeContext()]
            );

        $unsubscribeResult = $unsubscribeController->execute();

        $this->assertInstanceOf("\Magento\Framework\Controller\Result\Redirect", $unsubscribeResult);
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
            ->willReturnOnConsecutiveCalls(self::EMAIL, self::LIST, self::STORE);

        return $request;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function makeContext()
    {
        $context = $this->getMockBuilder(\Magento\Backend\App\Action\Context::class)->disableOriginalConstructor()->getMock();

        $context->expects($this->once())->method("getRequest")->willReturn($this->makeRequestObject());
        $context->expects($this->exactly(2))->method("getObjectManager")->willReturn($this->makeObjectManagerMock());
        $context->expects($this->exactly(2))->method("getMessageManager")->willReturn($this->makeMessageManagerMock());
        $context->expects($this->exactly(2))->method("getResultRedirectFactory")->willReturn($this->makeResultRedirectMock());

        return $context;
    }

    private function makeResultRedirectMock()
    {
        $resultRedirectFactoryMock = $this->getMockBuilder(\Magento\Framework\Controller\Result\RedirectFactory::class)
            ->setMethods(["create"])
            ->disableOriginalConstructor()
            ->getMock();

        $resultRedirectMock = $this->getMockBuilder(\Magento\Framework\Controller\Result\Redirect::class)
            ->disableOriginalConstructor()
            ->getMock();
        $resultRedirectMock->expects($this->once())->method("setPath")->with("/");

        $resultRedirectFactoryMock->expects($this->once())->method("create")->willReturn($resultRedirectMock);

        return $resultRedirectFactoryMock;
    }

    private function makeMessageManagerMock()
    {
        $messageManagerMock = $this->getMockBuilder(\Magento\Framework\Message\Manager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $messageManagerMock->expects($this->once())->method("addNotice")->with("You are unsubcribed from " . self::LIST);

        return $messageManagerMock;
    }

    private function makeObjectManagerMock()
    {
        $objectManagerMock = $this->getMockBuilder(\Magento\Framework\ObjectManager\ObjectManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $objectManagerMock->expects($this->exactly(2))->method("create")
            ->withConsecutive(
                ["\Ebizmarts\Mandrill\Model\Unsubscribe"],
                ["\Ebizmarts\Mandrill\Model\Unsubscribe"]
            )
            ->willReturnOnConsecutiveCalls(
                $this->makeMandrillUnsubsribeMockWithCollection(),
                $this->makeMandrillUnsubsribeMock()
            );

        return $objectManagerMock;
    }

    private function makeMandrillUnsubsribeMock()
    {
        $unsubscribeMock = $this->getMockBuilder(\Ebizmarts\Mandrill\Model\Unsubscribe::class)
            ->setMethods(["setEmail", "setList", "setStoreId", "save", "setUnsubscribedAt"])
            ->disableOriginalConstructor()
            ->getMock();
        $unsubscribeMock
            ->expects($this->once())
            ->method("setEmail")
            ->with(self::EMAIL)
            ->willReturnSelf();
        $unsubscribeMock
            ->expects($this->once())
            ->method("setList")
            ->with(self::LIST)
            ->willReturnSelf();
        $unsubscribeMock
            ->expects($this->once())
            ->method("setStoreId")
            ->with(self::STORE)
            ->willReturnSelf();
        $unsubscribeMock
            ->expects($this->once())
            ->method("setUnsubscribedAt")
            ->withAnyParameters()
            ->willReturnSelf();
        $unsubscribeMock
            ->expects($this->once())
            ->method("save")
            ->willReturnSelf();

        return $unsubscribeMock;
    }

    private function makeMandrillUnsubsribeMockWithCollection()
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
                ["main_table.email", ["eq" => self::EMAIL]],
                ["main_table.list", ["eq" => self::LIST]],
                ["main_table.store_id", ["eq" => self::STORE]]
            )
            ->willReturnSelf();

        $collectionMock->expects($this->once())->method("setPageSize")->with(1);
        $collectionMock->expects($this->once())->method("getSize")->willReturn(0);

        return $collectionMock;
    }
}
