<?php
/**
 * Author: info@ebizmarts.com
 * Date: 7/22/15
 * Time: 10:36 PM
 * File: Mailsent.php
 * Module: magento2-mandrill
 */
namespace Ebizmarts\Mandrill\Model;

class Mailsent extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Ebizmarts\Mandrill\Model\Resource\Mailsent');
    }

}