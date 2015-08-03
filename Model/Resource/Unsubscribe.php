<?php
/**
 * Author: info@ebizmarts.com
 * Date: 8/3/15
 * Time: 12:30 PM
 * File: Unsubscribe.php
 * Module: magento2-mandrill
 */
namespace Ebizmarts\Mandrill\Model\Resource;

class Unsubscribe extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mandrill_unsubscribe', 'id');
    }

}