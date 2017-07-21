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

class TransportInterfaceFactory
{
    /**
     * Mandrill Transport Factory
     *
     * @var \Ebizmarts\Mandrill\Model\TransportFactory
     */
    protected $mandrillTransportFactory;

    /**
     * Mandrill Helper class
     *
     * @var \Ebizmarts\Mandrill\Helper\Data
     */
    protected $mandrillHelper;

    /**
     * TransportBuilder constructor.
     * @param \Ebizmarts\Mandrill\Helper\Data $mandrillHelper
     * @param \Ebizmarts\Mandrill\Model\TransportFactory $mandrillTransportFactory
     */
    public function __construct(
        \Ebizmarts\Mandrill\Helper\Data $mandrillHelper,
        \Ebizmarts\Mandrill\Model\TransportFactory $mandrillTransportFactory
    ) {
        $this->mandrillHelper = $mandrillHelper;
        $this->mandrillTransportFactory = $mandrillTransportFactory;
    }

    /**
     * Replace mail transport with Mandrill if needed
     *
     * @param \Magento\Framework\Mail\TransportInterfaceFactory $subject
     * @param \Closure $proceed
     * @param array $data
     *
     * @return \Magento\Framework\Mail\TransportInterface
     */
    public function aroundCreate(
        \Magento\Framework\Mail\TransportInterfaceFactory $subject,
        \Closure $proceed,
        array $data = []
    ) {
        if ($this->isMandrillEnabled() === false) {
            /** @var \Magento\Framework\Mail\TransportInterface $transport */
            return $proceed($data);
        } else {
            return $this->mandrillTransportFactory->create($data);
        }
    }

    /**
     * Get status of Mandrill
     *
     * @return bool
     */
    private function isMandrillEnabled()
    {
        return $this->mandrillHelper->isMandrillEnabled();
    }
}
