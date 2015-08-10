<?php
/**
 * Author: info@ebizmarts.com
 * Date: 8/3/15
 * Time: 4:43 PM
 * File: View.php
 * Module: magento2-mandrill
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