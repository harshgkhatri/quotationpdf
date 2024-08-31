<?php
///**
// * Copyright © ApGroup_ Quotation All rights reserved.
// * See COPYING.txt for license details.
// */
//declare(strict_types=1);
//
//namespace ApGroup\Quotation\Block\Quotation;
//
//class Data extends \Magento\Framework\View\Element\Template
//{
//
//    /**
//     * Constructor
//     *
//     * @param \Magento\Framework\View\Element\Template\Context  $context
//     * @param array $data
//     */
//    public function __construct(
//        \Magento\Framework\View\Element\Template\Context $context,
//        array $data = []
//    ) {
//        parent::__construct($context, $data);
//    }
//}



namespace ApGroup\Quotation\Block\Quotation;

use Magento\Framework\View\Element\Template;
use ApGroup\Quotation\Model\ResourceModel\Product\CollectionFactory;

class Data extends Template
{
    protected $productCollectionFactory;

    public function __construct(
        Template\Context $context,
        CollectionFactory $productCollectionFactory,
        array $data = []
    ) {

        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);
    }

    public function getProducts()
    {
        return $this->productCollectionFactory->create()->getItems();
    }
}
