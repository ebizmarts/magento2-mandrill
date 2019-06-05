<?php

namespace Ebizmarts\Mandrill\Test\Integration;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\DeploymentConfig\Reader;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Module\ModuleList;
use Magento\Framework\App\ObjectManager;

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
}
