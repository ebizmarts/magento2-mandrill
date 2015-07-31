<?php
/**
 * Author: info@ebizmarts.com
 * Date: 7/22/15
 * Time: 10:35 PM
 * File: Mailsent.php
 * Module: magento2-mandrill
 */
namespace Ebizmarts\Mandrill\Model\Resource;

class Mailsent extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mandrill_mailsent', 'id');
    }

}