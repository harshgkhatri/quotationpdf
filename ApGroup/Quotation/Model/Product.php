<?php

namespace ApGroup\Quotation\Model;

use Magento\Framework\Model\AbstractModel;
use ApGroup\Quotation\Model\ResourceModel\Product as ResourceModel;

class Product extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
