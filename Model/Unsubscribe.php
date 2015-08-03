<?php
/**
 * Author: info@ebizmarts.com
 * Date: 8/3/15
 * Time: 12:29 PM
 * File: Unsubscribe.php
 * Module: magento2-mandrill
 */
namespace Ebizmarts\Mandrill\Model;

class Unsubscribe extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Ebizmarts\Mandrill\Model\Resource\Unsubscribe');
    }

}