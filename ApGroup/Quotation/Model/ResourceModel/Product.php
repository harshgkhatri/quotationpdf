<?php

namespace ApGroup\Quotation\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Product extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('ap_group_products', 'id'); // The table name and primary key column
    }
}
