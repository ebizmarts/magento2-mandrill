<?php
/**
 * Ebizmarts_Mandrill Magento JS component
 *
 * @category    Ebizmarts
 * @package     Ebizmarts_Mandrill
 * @author      Ebizmarts Team <info@ebizmarts.com>
 * @copyright   Ebizmarts (http://ebizmarts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Ebizmarts\Mandrill\Model\Plugin;

class Config
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    /**
     * @var \Magento\Framework\Module\ModuleList\Loader
     */
    protected $_loader;
    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $_writer;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Module\ModuleList\Loader $loader
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Module\ModuleList\Loader $loader,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
    )
    {
        $this->_objectManager = $objectManager;
        $this->_logger = $logger;
        $this->_loader = $loader;
        $this->_writer = $configWriter;
    }
    public function aroundSave(\Magento\Config\Model\Config $config,\Closure $proceed)
    {
        $ret = $proceed();
        $sectionId = $config->getSection();
        if($sectionId=='mandrill'&&!$config->getConfigDataValue('mandrill/general/active'))
        {
            $modules = $this->_loader->load();
            if(isset($modules['Ebizmarts_AbandonedCart']))
            {
                $this->_writer->save(\Ebizmarts\AbandonedCart\Model\Config::ACTIVE,0,$config->getScope(),$config->getScopeId());
            }
            if(isset($modules['Ebizmarts_AutoResponder']))
            {
                $this->_writer->save(\Ebizmarts\AutoResponder\Model\Config::ACTIVE,0,$config->getScope(),$config->getScopeId());
            }
        }
        return $ret;
    }
}
