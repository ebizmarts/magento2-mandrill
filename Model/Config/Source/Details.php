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


namespace Ebizmarts\Mandrill\Model\Config\Source;
use Mandrill_Error;


class Details implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var Mandrill|null
     */
    protected $_options = null;

    /**
     * Details constructor.
     * @param \Ebizmarts\Mandrill\Helper\Data $helper
     * @param \Ebizmarts\Mandrill\Model\Api\Mandrill $api
     */
    public function __construct(
        \Ebizmarts\Mandrill\Helper\Data $helper,
        \Ebizmarts\Mandrill\Model\Api\Mandrill $api
    )
    {
        $this->_helper  = $helper;
        $apiKey = $helper->getApiKey();
        if($apiKey) {
            try {
//                $this->_api     = New \Mandrill($apiKey);
                $this->_options = $api->getApi()->users->info();
            }
            catch(Mandrill_Error $e)
            {
                $this->_options = 'Invalid APIKEY';
            }
        }
    }

    public function toOptionArray()
    {
        if(is_array($this->_options)) {
            return [
                ['label'=>'User Name','value'=> $this->_options['username']],
                ['label'=>'Reputation',     'value'=> $this->_options['reputation']],
                ['label'=>'Hourly Quota',     'value'=>$this->_options['hourly_quota']],
                ['label'=>'Backlog',     'value'=>$this->_options['backlog']],
            ];
        }
        else {
            return [
                ['value'=>'Error','label'=>$this->_options]
            ];
        }
    }

}