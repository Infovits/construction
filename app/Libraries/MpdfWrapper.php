<?php 
namespace App\Libraries;

use Mpdf\Mpdf;

class MpdfWrapper
{
    protected $mpdf;

    public function __construct()
    {
        // Initialize mPDF
        $this->mpdf = new Mpdf();
    }

    public function generatePdf($htmlContent, $filename = 'output.pdf')
    {
        // Add the HTML content to mPDF
        $this->mpdf->WriteHTML($htmlContent);

        // Output the PDF
        $this->mpdf->Output($filename, 'D');
    }
}

    
    