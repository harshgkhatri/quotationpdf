<?php

namespace ApGroup\Quotation\Controller\Quotation;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use ApGroup\Quotation\Model\ProductFactory;
use Magento\Framework\App\ResourceConnection;

class ProductSave extends Action
{
    protected $resultJsonFactory;
    protected $productFactory;
    protected $resource;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ProductFactory $productFactory,
        ResourceConnection $resource
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productFactory = $productFactory;
        $this->resource = $resource;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        try {
            // Get the request data
            $data = $this->getRequest()->getPostValue();

            // Validate and sanitize the input data
            if (empty($data['product_name']) || empty($data['product_gst']) || empty($data['price'])) {
                throw new LocalizedException(__('Please fill in all required fields.'));
            }

            // Create a new product model instance
            $product = $this->productFactory->create();

            // Load product if an ID is provided (for updating)
            if (!empty($data['quotation_id'])) {
                $product->load($data['quotation_id']);
                if (!$product->getId()) {
                    throw new LocalizedException(__('The product does not exist.'));
                }
            }

            // Set product data
            $product->setData('product_name', $data['product_name']);
            $product->setData('product_gst', $data['product_gst']);
            $product->setData('price', $data['price']);

            // Save the product
            $product->save();

            $result->setData([
                'status' => true,
                'message' => __('Product has been saved successfully.')
            ]);

        } catch (LocalizedException $e) {
            $result->setData([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            $result->setData([
                'status' => false,
                'message' => __('An error occurred while saving the product.')
            ]);
        }

        return $result;
    }
}
