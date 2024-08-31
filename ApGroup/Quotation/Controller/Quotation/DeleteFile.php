<?php

namespace ApGroup\Quotation\Controller\Quotation;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Filesystem\Driver\File as FileDriver;

class DeleteFile extends Action
{
    protected $jsonFactory;
    protected $fileDriver;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        FileDriver $fileDriver
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->fileDriver = $fileDriver;
        parent::__construct($context);
    }

    public function execute()
    {
        $fileUrl = $this->getRequest()->getParam('file_url', '');
        $filePath = parse_url($fileUrl, PHP_URL_PATH);
        $filePath = BP . '/pub' . $filePath;

        $result = $this->jsonFactory->create();

        if ($this->fileDriver->isExists($filePath)) {
            try {
                $this->fileDriver->deleteFile($filePath);
                $result->setData(['status' => true, 'message' => 'File deleted successfully.']);
            } catch (\Exception $e) {
                $result->setData(['status' => false, 'message' => 'Failed to delete file.']);
            }
        } else {
            $result->setData(['status' => false, 'message' => 'File not found.']);
        }

        return $result;
    }
}
