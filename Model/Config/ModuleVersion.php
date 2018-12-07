<?php
declare(strict_types=1);

namespace Ebizmarts\Mandrill\Model\Config;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Directory\ReadFactory;

/**
 * Class ModuleVersion
 * @see https://magento.stackexchange.com/a/174168/252
 */
class ModuleVersion
{
    const COMPOSER_FILE_NAME = 'composer.json';
    /**
     * @var ComponentRegistrarInterface
     */
    private $componentRegistrar;
    /**
     * @var ReadFactory
     */
    private $readFactory;

    public function __construct(ComponentRegistrarInterface $componentRegistrar, ReadFactory $readFactory)
    {
        $this->componentRegistrar = $componentRegistrar;
        $this->readFactory        = $readFactory;
    }

    /**
     * Get module composer version
     *
     * @param string $moduleName
     * @return string
     */
    public function getModuleVersion($moduleName): string
    {
        $emptyVersionNumber = '';
        $composerJsonData   = null;
        try {
            $path             = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, $moduleName);
            $directoryRead    = $this->readFactory->create($path);
            $composerJsonData = $directoryRead->readFile(self::COMPOSER_FILE_NAME);
        } catch (\LogicException $pathException) {
            return $emptyVersionNumber;
        } catch (FileSystemException $fsException) {
            return $emptyVersionNumber;
        }
        $jsonData = json_decode($composerJsonData);
        if ($jsonData === null) {
            return $emptyVersionNumber;
        }

        return $jsonData->version ?? $emptyVersionNumber;
    }
}
