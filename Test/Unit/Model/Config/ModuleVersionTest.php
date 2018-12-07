<?php
declare(strict_types=1);

namespace Ebizmarts\Mandrill\Test\Unit\Model\Config;

use Ebizmarts\Mandrill\Model\Config\ModuleVersion;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class ModuleVersionTest extends \PHPUnit\Framework\TestCase
{
    private $objectManagerHelper;
    const MODULE_DIR = '/some/path/to/module/dir';
    const MODULE_NAME = 'Ebizmarts_Mandrill';
    const COMPOSER_JSON_FILENAME = 'composer.json';

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManager($this);
    }

    public function testGetModuleVersionInvalidJson()
    {
        $registrarMock = $this->makeRegistrarMock();
        $readMock      = $this->getMockBuilder(Read::class)->disableOriginalConstructor()->getMock();
        $readMock
            ->expects($this->once())
            ->method('readFile')
            ->with(self::COMPOSER_JSON_FILENAME)
            ->willReturn('not a json file');
        $readFactoryMock = $this->makeReadFactoryMock($readMock);
        /** @var \Ebizmarts\Mandrill\Model\Config\ModuleVersion $sut */
        $sut           = $this->objectManagerHelper->getObject(ModuleVersion::class, [
                'componentRegistrar' => $registrarMock,
                'readFactory'        => $readFactoryMock,
            ]);
        $moduleVersion = $sut->getModuleVersion(self::MODULE_NAME);
        $this->assertEquals('', $moduleVersion);
    }

    public function testGetModuleVersionNoVersionInComposerJson()
    {
        $registrarMock = $this->makeRegistrarMock();
        $readMock      = $this->getMockBuilder(Read::class)->disableOriginalConstructor()->getMock();
        $readMock
            ->expects($this->once())
            ->method('readFile')
            ->with(self::COMPOSER_JSON_FILENAME)
            ->willReturn('{
                    "name": "ebizmarts/magento2-mandrill",
                    "description": "Connect Mandrill with Magento"
                  }');
        $readFactoryMock = $this->makeReadFactoryMock($readMock);
        /** @var \Ebizmarts\Mandrill\Model\Config\ModuleVersion $sut */
        $sut           = $this->objectManagerHelper->getObject(ModuleVersion::class, [
                'componentRegistrar' => $registrarMock,
                'readFactory'        => $readFactoryMock,
            ]);
        $moduleVersion = $sut->getModuleVersion(self::MODULE_NAME);
        $this->assertEquals('', $moduleVersion);
    }

    public function testGetModuleVersionOk()
    {
        $registrarMock = $this->makeRegistrarMock();
        $readMock      = $this->getMockBuilder(Read::class)->disableOriginalConstructor()->getMock();
        $readMock->expects($this->once())->method('readFile')->with(self::COMPOSER_JSON_FILENAME)->willReturn('{
                    "name": "ebizmarts/magento2-mandrill",
                    "version": "1.2.9",
                    "description": "Connect Mandrill with Magento"
                }');
        $readFactoryMock = $this->makeReadFactoryMock($readMock);
        /** @var \Ebizmarts\Mandrill\Model\Config\ModuleVersion $sut */
        $sut           = $this->objectManagerHelper->getObject(ModuleVersion::class, [
                'componentRegistrar' => $registrarMock,
                'readFactory'        => $readFactoryMock,
            ]);
        $moduleVersion = $sut->getModuleVersion(self::MODULE_NAME);
        $this->assertEquals('1.2.9', $moduleVersion);
    }

    public function testGetModuleVersionPathException()
    {
        $registrarMock = $this->getMockBuilder(ComponentRegistrarInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $registrarMock->expects($this->once())->method('getPath')->with('module',
                self::MODULE_NAME)->willThrowException(new \LogicException('is not a valid component type'));
        $readFactoryMock = $this
            ->getMockBuilder('\Magento\Framework\Filesystem\Directory\ReadFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $readFactoryMock->expects($this->never())->method('create');
        /** @var \Ebizmarts\Mandrill\Model\Config\ModuleVersion $sut */
        $sut           = $this->objectManagerHelper->getObject(ModuleVersion::class, [
                'componentRegistrar' => $registrarMock,
                'readFactory'        => $readFactoryMock,
            ]);
        $moduleVersion = $sut->getModuleVersion(self::MODULE_NAME);
        $this->assertEquals('', $moduleVersion);
    }

    public function testGetModuleVersionReadFileException()
    {
        $registrarMock = $this->makeRegistrarMock();
        $readMock      = $this->getMockBuilder(Read::class)->disableOriginalConstructor()->getMock();
        $readMock->expects($this->once())->method('readFile')->with(self::COMPOSER_JSON_FILENAME)
            ->willThrowException(
                new \Magento\Framework\Exception\FileSystemException(
                    new \Magento\Framework\Phrase('No such file or directory')
                )
            );
        $readFactoryMock = $this->makeReadFactoryMock($readMock);
        /** @var \Ebizmarts\Mandrill\Model\Config\ModuleVersion $sut */
        $sut           = $this->objectManagerHelper->getObject(ModuleVersion::class, [
                'componentRegistrar' => $registrarMock,
                'readFactory'        => $readFactoryMock,
            ]);
        $moduleVersion = $sut->getModuleVersion(self::MODULE_NAME);
        $this->assertEquals('', $moduleVersion);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function makeRegistrarMock()
    {
        $registrarMock = $this
            ->getMockBuilder(ComponentRegistrarInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $registrarMock->expects($this->once())->method('getPath')->with('module',
                self::MODULE_NAME)->willReturn(self::MODULE_DIR);

        return $registrarMock;
    }

    /**
     * @param $readMock
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function makeReadFactoryMock($readMock)
    {
        $readFactoryMock = $this
            ->getMockBuilder('\Magento\Framework\Filesystem\Directory\ReadFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $readFactoryMock->expects($this->once())->method('create')->with(self::MODULE_DIR)->willReturn($readMock);

        return $readFactoryMock;
    }
}