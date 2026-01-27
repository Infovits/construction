<?php 
namespace App\Libraries;

use Mpdf\Mpdf;

class MpdfWrapper
{
    protected $mpdf;
    protected $companyInfo;

    public function __construct($companyInfo = null)
    {
        // Set company info with fallback
        $this->companyInfo = $companyInfo ?? [
            'name' => 'Construction Management System',
            'address' => 'Default Address',
            'logo' => null
        ];
        
        // Initialize mPDF with custom configuration
        $this->mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_header' => 8,
            'margin_footer' => 8,
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 40,
            'margin_bottom' => 20
        ]);
        
        // Set document properties
        $this->mpdf->SetTitle('Inventory Report');
        $this->mpdf->SetAuthor($this->companyInfo['name'] ?? 'Construction Management System');
        
        // Add default header if company info is provided
        if ($this->companyInfo && !empty($this->companyInfo['name'])) {
            $this->setDefaultHeader();
        }
        
        // Set default footer
        $this->setDefaultFooter();
    }
    
    /**
     * Set default header with company info
     */
    private function setDefaultHeader()
    {
        $companyName = $this->companyInfo['name'] ?? '';
        $companyLogo = $this->companyInfo['logo'] ?? '';
        $companyAddress = $this->companyInfo['address'] ?? '';
        
        $header = '<table width="100%" style="border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 10px;">';
        $header .= '<tr>';
        
        if (!empty($companyLogo)) {
            $header .= '<td width="20%" style="text-align: left;"><img src="' . $companyLogo . '" height="50"></td>';
            $header .= '<td width="80%" style="text-align: right;">';
        } else {
            $header .= '<td width="100%" style="text-align: right;">';
        }
        
        $header .= '<span style="font-size: 20px; font-weight: bold;">' . $companyName . '</span><br>';
        $header .= '<span style="font-size: 12px;">' . $companyAddress . '</span>';
        $header .= '</td></tr></table>';
        
        $this->mpdf->SetHTMLHeader($header);
    }
    
    /**
     * Set default footer with page numbers
     */
    private function setDefaultFooter()
    {
        $date = date('Y-m-d H:i:s');
        $footer = '<table width="100%" style="border-top: 1px solid #ddd; padding-top: 5px; font-size: 9pt;">';
        $footer .= '<tr>';
        $footer .= '<td width="33%" style="text-align: left;">Generated on: ' . $date . '</td>';
        $footer .= '<td width="33%" style="text-align: center;">Inventory Management System</td>';
        $footer .= '<td width="33%" style="text-align: right;">Page {PAGENO} of {nbpg}</td>';
        $footer .= '</tr></table>';
        
        $this->mpdf->SetHTMLFooter($footer);
    }

    /**
     * Generate PDF with custom content
     * 
     * @param string $htmlContent The HTML content
     * @param string $filename The output filename
     * @param string $outputMode Output mode ('D' = download, 'I' = inline, 'F' = file)
     * @return mixed PDF output based on mode
     */
    public function generatePdf($htmlContent, $filename = 'output.pdf', $outputMode = 'D')
    {
        // Add the HTML content to mPDF
        $this->mpdf->WriteHTML($htmlContent);

        // Output the PDF
        return $this->mpdf->Output($filename, $outputMode);
    }
    
    /**
     * Generate stock movement report PDF
     * 
     * @param array $data Stock movement data
     * @param array $filters Report filters
     * @return string PDF output
     */
    public function generateStockMovementReport($data, $filters = [])
    {
        // Set title
        $title = 'Stock Movement Report';
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $title .= ' (' . $filters['start_date'] . ' to ' . $filters['end_date'] . ')';
        }
        
        // Start HTML content
        $html = '<h1 style="text-align: center;">' . $title . '</h1>';
        
        // Add filters summary if any
        if (!empty($filters)) {
            $html .= '<div style="margin-bottom: 20px; font-size: 12px;">';
            $html .= '<strong>Filters: </strong>';
            
            $filterTexts = [];
            if (!empty($filters['warehouse_id'])) {
                $filterTexts[] = 'Warehouse: ' . $filters['warehouse_name'];
            }
            if (!empty($filters['material_id'])) {
                $filterTexts[] = 'Material: ' . $filters['material_name'];
            }
            if (!empty($filters['movement_type'])) {
                $filterTexts[] = 'Movement Type: ' . $filters['movement_type'];
            }
            
            $html .= implode(' | ', $filterTexts);
            $html .= '</div>';
        }
        
        // Check if data exists
        if (empty($data)) {
            $html .= '<div style="text-align: center; padding: 20px; color: #777;">No stock movement data found matching your criteria.</div>';
            return $this->generatePdf($html, 'stock_movement_report.pdf');
        }
        
        // Create table
        $html .= '<table style="width: 100%; border-collapse: collapse; font-size: 12px;">';
        $html .= '<thead>';
        $html .= '<tr style="background-color: #f3f4f6;">';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Date</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Material</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Warehouse</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Type</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Quantity</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Project</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">User</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        foreach ($data as $movement) {
            $html .= '<tr>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . date('Y-m-d H:i', strtotime($movement['created_at'])) . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $movement['name'] . ' (' . $movement['item_code'] . ')</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $movement['warehouse_name'] . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $movement['movement_type'] . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">' . $movement['quantity'] . ' ' . $movement['unit'] . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . ($movement['project_name'] ?? 'N/A') . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $movement['first_name'] . ' ' . $movement['last_name'] . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody>';
        $html .= '</table>';
        
        return $this->generatePdf($html, 'stock_movement_report.pdf');
    }
    
    /**
     * Generate project usage report PDF
     * 
     * @param array $data Project usage data
     * @param array $filters Report filters
     * @return string PDF output
     */
    public function generateProjectUsageReport($data, $filters = [])
    {
        // Set title
        $title = 'Project Material Usage Report';
        if (!empty($filters['project_name'])) {
            $title .= ' - ' . $filters['project_name'];
        }
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $title .= ' (' . $filters['start_date'] . ' to ' . $filters['end_date'] . ')';
        }
        
        // Start HTML content
        $html = '<h1 style="text-align: center;">' . $title . '</h1>';
        
        // Add filters summary if any
        if (!empty($filters)) {
            $html .= '<div style="margin-bottom: 20px; font-size: 12px;">';
            $html .= '<strong>Filters: </strong>';
            
            $filterTexts = [];
            if (!empty($filters['category_id'])) {
                $filterTexts[] = 'Category: ' . $filters['category_name'];
            }
            if (!empty($filters['material_id'])) {
                $filterTexts[] = 'Material: ' . $filters['material_name'];
            }
            
            $html .= implode(' | ', $filterTexts);
            $html .= '</div>';
        }
        
        // Check if data exists
        if (empty($data)) {
            $html .= '<div style="text-align: center; padding: 20px; color: #777;">No project usage data found matching your criteria.</div>';
            return $this->generatePdf($html, 'project_usage_report.pdf');
        }
        
        // Calculate totals
        $totalCost = 0;
        foreach ($data as $usage) {
            $totalCost += $usage['unit_cost'] * $usage['quantity'];
        }
        
        // Create table
        $html .= '<table style="width: 100%; border-collapse: collapse; font-size: 12px;">';
        $html .= '<thead>';
        $html .= '<tr style="background-color: #f3f4f6;">';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Date</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Material</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Category</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Quantity Used</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Unit Cost</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Total Cost</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Recorded By</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        foreach ($data as $usage) {
            $itemCost = $usage['unit_cost'] * $usage['quantity'];
            $html .= '<tr>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . date('Y-m-d', strtotime($usage['created_at'])) . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $usage['name'] . ' (' . $usage['item_code'] . ')</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $usage['category_name'] . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">' . $usage['quantity'] . ' ' . $usage['unit'] . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">' . number_format($usage['unit_cost'], 2) . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">' . number_format($itemCost, 2) . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $usage['first_name'] . ' ' . $usage['last_name'] . '</td>';
            $html .= '</tr>';
        }
        
        // Add summary row
        $html .= '<tr style="font-weight: bold; background-color: #f3f4f6;">';
        $html .= '<td colspan="5" style="border: 1px solid #ddd; padding: 8px; text-align: right;">Total Cost:</td>';
        $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">' . number_format($totalCost, 2) . '</td>';
        $html .= '<td style="border: 1px solid #ddd; padding: 8px;"></td>';
        $html .= '</tr>';
        
        $html .= '</tbody>';
        $html .= '</table>';
        
        return $this->generatePdf($html, 'project_usage_report.pdf');
    }
    
    /**
     * Generate low stock report PDF
     * 
     * @param array $data Low stock data
     * @param array $filters Report filters
     * @return string PDF output
     */
    public function generateLowStockReport($data, $filters = [])
    {
        // Set title
        $title = 'Low Stock Report';
        
        // Start HTML content
        $html = '<h1 style="text-align: center;">' . $title . '</h1>';
        
        // Add filters summary if any
        if (!empty($filters)) {
            $html .= '<div style="margin-bottom: 20px; font-size: 12px;">';
            $html .= '<strong>Filters: </strong>';
            
            $filterTexts = [];
            if (!empty($filters['warehouse_id'])) {
                $filterTexts[] = 'Warehouse: ' . $filters['warehouse_name'];
            }
            if (!empty($filters['category_id'])) {
                $filterTexts[] = 'Category: ' . $filters['category_name'];
            }
            
            $html .= implode(' | ', $filterTexts);
            $html .= '</div>';
        }
        
        // Check if data exists
        if (empty($data)) {
            $html .= '<div style="text-align: center; padding: 20px; color: #777;">No low stock items found matching your criteria.</div>';
            return $this->generatePdf($html, 'low_stock_report.pdf');
        }
        
        // Create table
        $html .= '<table style="width: 100%; border-collapse: collapse; font-size: 12px;">';
        $html .= '<thead>';
        $html .= '<tr style="background-color: #f3f4f6;">';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Material</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Category</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Warehouse</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Current Stock</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Minimum Stock</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Reorder Level</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Status</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        foreach ($data as $item) {
            $isCritical = $item['current_quantity'] <= $item['minimum_quantity'] / 2;
            $statusStyle = $isCritical ? 'color: red; font-weight: bold;' : 'color: orange;';
            
            $html .= '<tr>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $item['name'] . ' (' . $item['item_code'] . ')</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $item['category_name'] . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . ($item['warehouse_name'] ?? 'All Warehouses') . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">' . $item['current_quantity'] . ' ' . $item['unit'] . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">' . $item['minimum_quantity'] . ' ' . $item['unit'] . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">' . $item['reorder_level'] . ' ' . $item['unit'] . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: center; ' . $statusStyle . '">' . 
                     ($isCritical ? 'CRITICAL' : 'LOW') . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody>';
        $html .= '</table>';
        
        return $this->generatePdf($html, 'low_stock_report.pdf');
    }
}