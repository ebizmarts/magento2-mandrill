<?php

namespace Ebizmarts\Mandrill\Test\Integration;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\DeploymentConfig\Reader;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Module\ModuleList;
use Magento\TestFramework\ObjectManager;

class ModuleConfigTest extends \PHPUnit\Framework\TestCase
{
    const MODULE_NAME = 'Ebizmarts_Mandrill';

    /**
     * @var $objectManager ObjectManager
     */
    private $objectManager;

    protected function setUp()
    {
        /** @var ObjectManager objectManager */
        $this->objectManager = ObjectManager::getInstance();
    }

    public function testTheModuleIsRegistered()
    {
        $registrar = new ComponentRegistrar();
        $this->assertArrayHasKey(self::MODULE_NAME, $registrar->getPaths(ComponentRegistrar::MODULE));
    }

    public function testTheModuleIsConfiguredInTheTestEnvironment()
    {
        /** @var $moduleList ModuleList */
        $moduleList = $this->objectManager->create(ModuleList::class);
        $this->assertTrue($moduleList->has(self::MODULE_NAME));
    }

    public function testTheModuleIsConfiguredInTheRealEnvironment()
    {
        // The tests by default point to the wrong config directory for this test.
        $directoryList = $this->objectManager->create(
            DirectoryList::class,
            ['root' => BP]
        );

        /** @var \Magento\Framework\App\DeploymentConfig\Reader $deploymentConfigReader */
        $deploymentConfigReader = $this->objectManager->create(
            Reader::class,
            ['dirList' => $directoryList]
        );

        /** @var \Magento\Framework\App\DeploymentConfig $deploymentConfig */
        $deploymentConfig = $this->objectManager->create(
            DeploymentConfig::class,
            ['reader' => $deploymentConfigReader]
        );

        /** @var $moduleList ModuleList */
        $moduleList = $this->objectManager->create(
            ModuleList::class,
            ['config' => $deploymentConfig]
        );
        $this->assertTrue($moduleList->has(self::MODULE_NAME));

        $moduleInformation = $moduleList->getOne(self::MODULE_NAME);
        $this->assertArrayHasKey("sequence", $moduleInformation);
        $this->assertCount(1, $moduleInformation["sequence"]);
        $this->assertEquals("Magento_Config", $moduleInformation["sequence"][0]);
    }
}
