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
    protected $_api     = null;
    protected $_options = null;
    protected $_helper  = null;

    /**
     * @param \Ebizmarts\Mandrill\Helper\Data $helper
     */
    public function __construct(\Ebizmarts\Mandrill\Helper\Data $helper)
    {
        $this->_helper  = $helper;
        $apiKey = $helper->getApiKey();
        if($apiKey) {
            try {
                $this->_api     = New \Mandrill($apiKey);
                $this->_options = $this->_api->users->info();
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
                ['value'=>'User Name','label'=> $this->_options['username']],
                ['value'=>'Reputation',     'label'=> $this->_options['reputation']],
                ['value'=>'Hourly Quota',     'label'=>$this->_options['hourly_quota']],
                ['value'=>'Backlog',     'label'=>$this->_options['backlog']],
            ];
        }
        else {
            return [
                ['value'=>'Error','label'=>$this->_options]
            ];
        }
    }
    public function toArray()
    {
        return array(
            'Account Name' => $this->_options->account_name
        );

    }

}