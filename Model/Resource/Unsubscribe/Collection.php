<?php
/**
 * Author: info@ebizmarts.com
 * Date: 8/3/15
 * Time: 12:31 PM
 * File: Collection.php
 * Module: magento2-mandrill
 */
namespace Ebizmarts\Mandrill\Model\Resource\Unsubscribe;

class Collection   extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ebizmarts\Mandrill\Model\Unsubscribe', 'Ebizmarts\Mandrill\Model\Resource\Unsubscribe');
    }

}