<?php

namespace ApGroup\Quotation\Model\ResourceModel\Product;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use ApGroup\Quotation\Model\Product as Model;
use ApGroup\Quotation\Model\ResourceModel\Product as ResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
