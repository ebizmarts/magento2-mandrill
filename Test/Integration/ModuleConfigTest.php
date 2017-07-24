<?php

namespace Ebizmarts\Mandrill\Test\Integration;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Module\ModuleList;
use Magento\TestFramework\ObjectManager;

class ModuleConfigTest extends \PHPUnit_Framework_TestCase
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
            \Magento\Framework\App\Filesystem\DirectoryList::class,
            ['root' => BP]
        );
        $deploymentConfigReader = $this->objectManager->create(
            \Magento\Framework\App\DeploymentConfig\Reader::class,
            ['dirList' => $directoryList]
        );
        $deploymentConfig = $this->objectManager->create(
            \Magento\Framework\App\DeploymentConfig::class,
            ['reader' => $deploymentConfigReader]
        );

        /** @var $moduleList ModuleList */
        $moduleList = $this->objectManager->create(
            ModuleList::class,
            ['config' => $deploymentConfig]
        );
        $this->assertTrue($moduleList->has(self::MODULE_NAME));
    }
}
