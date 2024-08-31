<?php

namespace ApGroup\Quotation\Controller\Quotation;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Zend_Pdf;
use Zend_Pdf_Style;
use Zend_Pdf_Color_Rgb;
use Zend_Pdf_Font;
use Zend_Pdf_Page;

class Save extends Action
{
    protected $jsonFactory;
    protected $date;
    protected $directoryList;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        DateTime $date,
        DirectoryList $directoryList
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->date = $date;
        $this->directoryList = $directoryList;
        parent::__construct($context);
    }

    public function execute()
    {
        // Get the data from the request
        $partyName = $this->getRequest()->getParam('party_name', '');
        $quotationData = $this->getRequest()->getParam('quotation', []);

        // Create new PDF
        $pdf = new Zend_Pdf();
        $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $pdf->pages[] = $page;

        // Set up styles and font
        $style = new Zend_Pdf_Style();
        $style->setLineColor(new Zend_Pdf_Color_Rgb(0, 0, 0));
        $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
        $style->setFont($font, 16); // Bold and larger font for the company name
        $page->setStyle($style);

        // Start drawing on the PDF
        $yPosition = 800;

        // Company Information
        $page->drawText("AP Group", 50, $yPosition, 'UTF-8'); // Bold company name
        $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA), 12);
        $page->setStyle($style);
        $page->drawText("Akash Prajapati", 50, $yPosition - 20, 'UTF-8');
        $page->drawText("160 Madhav Mall Opp Ratanba School", 50, $yPosition - 40, 'UTF-8');
        $page->drawText("Ahmedabad", 50, $yPosition - 60, 'UTF-8');
        $page->drawText("📱 7698063790 ✉️ akhpra96@gmail.com", 50, $yPosition - 80, 'UTF-8');
        $style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 12); // Bold for GSTIN
        $page->setStyle($style);
        $page->drawText("GSTIN 24ETDPP7679J1ZY", 50, $yPosition - 100, 'UTF-8');

        // Draw a line after the company information
        $page->setLineWidth(0.5);
        $page->drawLine(50, $yPosition - 110, $page->getWidth() - 50, $yPosition - 110);

        // Quotation Title
        $page->drawText("Quotation for " . $partyName, 400, $yPosition, 'UTF-8'); // Display the party name here
        $date = $this->date->gmtDate();
        $page->drawText("Date:-".$date, 450, $yPosition - 40, 'UTF-8');

        // Table Header
        $yPosition -= 150;
        $page->drawText("#", 50, $yPosition, 'UTF-8');
        $page->drawText("Description", 80, $yPosition, 'UTF-8');
        $page->drawText("Qty", 300, $yPosition, 'UTF-8');
        $page->drawText("Price", 350, $yPosition, 'UTF-8');
        $page->drawText("GST", 450, $yPosition, 'UTF-8');
        $page->drawText("Total", 500, $yPosition, 'UTF-8');

        $yPosition -= 20;

        $index = 1;
        foreach ($quotationData as $item) {
            $page->drawText($index++, 50, $yPosition, 'UTF-8');
            $page->drawText($item['product'], 80, $yPosition, 'UTF-8');
            $page->drawText($item['quantity'], 300, $yPosition, 'UTF-8');
            $page->drawText("₹ " . $item['price'], 350, $yPosition, 'UTF-8');
            $page->drawText($item['gst'], 450, $yPosition, 'UTF-8');
            $page->drawText("₹ " . $item['total_price'], 500, $yPosition, 'UTF-8');

            $yPosition -= 20;
        }

        // Assuming the last item has the grand total information, you can add it at the bottom
        $grandTotal = end($quotationData)['grand_total'];
        $page->drawText("GRAND TOTAL:", 400, $yPosition - 20, 'UTF-8');
        $page->drawText("₹ " . number_format($grandTotal, 2), 500, $yPosition - 20, 'UTF-8');

        // Footer
        $yPosition -= 60;
        $page->drawText("Terms & Conditions:", 50, $yPosition, 'UTF-8');
        $page->drawText("1 YEAR WARRANTY", 50, $yPosition - 20, 'UTF-8');

        $page->drawText("For, AP GROUP", 400, $yPosition - 60, 'UTF-8');
        $page->drawText("AUTHORIZED SIGNATURE", 400, $yPosition - 80, 'UTF-8');

        // Save the PDF to a temporary file
        $fileName = 'quotation_' . time() . '.pdf';
        $mediaPath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA) . '/quotations/';

// Ensure the directory existsa
        if (!is_dir($mediaPath)) {
            mkdir($mediaPath, 0777, true);
        }

        $filePath = $mediaPath . $fileName;
        file_put_contents($filePath, $pdf->render());

        $fileUrl = $this->_url->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_WEB]) . 'media/quotations/' . $fileName;

        // Return JSON response with the file URL
        $result = $this->jsonFactory->create();
        $result->setData([
            'status' => true,
            'message' => 'Quotation saved successfully!',
            'file_url' => $fileUrl
        ]);

        return $result;
    }
}
