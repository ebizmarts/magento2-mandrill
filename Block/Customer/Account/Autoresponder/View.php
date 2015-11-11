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

namespace Ebizmarts\Mandrill\Block\Customer\Account\Autoresponder;

class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'customer\list.phtml';

    public function getLists()
    {
        $lists = [];

        return $lists;
    }
    public function getSaveUrl()
    {
        $url = $this->getUrl('review/product/post', ['id' => 5]);
        return $url;
    }
}