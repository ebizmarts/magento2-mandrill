<?php
/**
 * Author: info@ebizmarts.com
 * Date: 7/22/15
 * Time: 10:36 PM
 * File: Collection.php
 * Module: magento2-mandrill
 */
namespace Ebizmarts\Mandrill\Model\Resource\Mailsent;

class Collection  extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ebizmarts\Mandrill\Model\Mailsent', 'Ebizmarts\Mandrill\Model\Resource\Mailsent');
    }

}