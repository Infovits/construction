<?php
namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;

class DomPDFWrapper
{
    protected $dompdf;
    protected $companyInfo;

    public function __construct($companyInfo = null)
    {
        // Set company info with fallback
        $this->companyInfo = $companyInfo ?? [
            'name' => 'Construction Management System',
            'address' => 'Default Address',
            'logo' => null
        ];
        
        // Configure DomPDF options
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isFontSubsettingEnabled', true);
        $options->set('dpi', 150);
        $options->set('defaultPaperSize', 'A4');
        $options->set('defaultPaperOrientation', 'portrait');
        
        // Initialize DomPDF with options
        $this->dompdf = new Dompdf($options);
        
        // Set document properties
        $this->dompdf->setPaper('A4', 'portrait');
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
        // Add the HTML content to DomPDF
        $this->dompdf->loadHtml($htmlContent);
        
        // Render the PDF
        $this->dompdf->render();
        
        // Output the PDF based on mode
        switch ($outputMode) {
            case 'D': // Download
                $this->dompdf->stream($filename, ['Attachment' => 1]);
                break;
            case 'I': // Inline (display in browser)
                $this->dompdf->stream($filename, ['Attachment' => 0]);
                break;
            case 'F': // Save to file
                file_put_contents($filename, $this->dompdf->output());
                break;
            default:
                // Default to download
                $this->dompdf->stream($filename, ['Attachment' => 1]);
        }
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
        
        // Start HTML content with CSS styling
        $html = $this->getPDFHeader($title);
        $html .= $this->getFiltersSummary($filters);
        
        // Check if data exists
        if (empty($data)) {
            $html .= '<div style="text-align: center; padding: 20px; color: #777; font-size: 16px;">No stock movement data found matching your criteria.</div>';
            return $this->generatePdf($html, 'stock_movement_report.pdf');
        }
        
        // Create table
        $html .= '<table class="data-table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Date</th>';
        $html .= '<th>Material</th>';
        $html .= '<th>Source Warehouse</th>';
        $html .= '<th>Destination Warehouse</th>';
        $html .= '<th>Type</th>';
        $html .= '<th>Quantity</th>';
        $html .= '<th>Project</th>';
        $html .= '<th>User</th>';
        $html .= '<th>Notes</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        foreach ($data as $movement) {
            $html .= '<tr>';
            $html .= '<td>' . date('Y-m-d H:i', strtotime($movement['created_at'])) . '</td>';
            $html .= '<td>' . ($movement['material_name'] ?? $movement['name'] ?? 'Unknown') . ' (' . ($movement['item_code'] ?? 'N/A') . ')</td>';
            $html .= '<td>' . ($movement['source_warehouse_name'] ?? 'N/A') . '</td>';
            $html .= '<td>' . ($movement['destination_warehouse_name'] ?? 'N/A') . '</td>';
            $html .= '<td>' . ($movement['movement_type'] ?? 'N/A') . '</td>';
            $html .= '<td style="text-align: right;">' . ($movement['quantity'] ?? 0) . ' ' . ($movement['unit'] ?? '') . '</td>';
            $html .= '<td>' . ($movement['project_name'] ?? 'N/A') . '</td>';
            $html .= '<td>' . ($movement['performed_by_name'] ?? 'N/A') . '</td>';
            $html .= '<td>' . ($movement['notes'] ?? 'N/A') . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= $this->getPDFFooter();
        
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
        $html = $this->getPDFHeader($title);
        $html .= $this->getFiltersSummary($filters);
        
        // Check if data exists
        if (empty($data)) {
            $html .= '<div style="text-align: center; padding: 20px; color: #777; font-size: 16px;">No project usage data found matching your criteria.</div>';
            return $this->generatePdf($html, 'project_usage_report.pdf');
        }
        
        // Calculate totals
        $totalCost = 0;
        foreach ($data as $usage) {
            $totalCost += $usage['unit_cost'] * $usage['quantity'];
        }
        
        // Create table
        $html .= '<table class="data-table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Date</th>';
        $html .= '<th>Material</th>';
        $html .= '<th>Category</th>';
        $html .= '<th>Quantity Used</th>';
        $html .= '<th>Unit Cost</th>';
        $html .= '<th>Total Cost</th>';
        $html .= '<th>Recorded By</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        foreach ($data as $usage) {
            $itemCost = $usage['unit_cost'] * $usage['quantity'];
            $html .= '<tr>';
            $html .= '<td>' . date('Y-m-d', strtotime($usage['created_at'])) . '</td>';
            $html .= '<td>' . $usage['name'] . ' (' . $usage['item_code'] . ')</td>';
            $html .= '<td>' . $usage['category_name'] . '</td>';
            $html .= '<td style="text-align: right;">' . $usage['quantity'] . ' ' . $usage['unit'] . '</td>';
            $html .= '<td style="text-align: right;">' . number_format($usage['unit_cost'], 2) . '</td>';
            $html .= '<td style="text-align: right;">' . number_format($itemCost, 2) . '</td>';
            $html .= '<td>' . ($usage['performed_by_name'] ?? 'N/A') . '</td>';
            $html .= '</tr>';
        }
        
        // Add summary row
        $html .= '<tr class="summary-row">';
        $html .= '<td colspan="5" style="text-align: right; font-weight: bold;">Total Cost:</td>';
        $html .= '<td style="text-align: right; font-weight: bold;">' . number_format($totalCost, 2) . '</td>';
        $html .= '<td></td>';
        $html .= '</tr>';
        
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= $this->getPDFFooter();
        
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
        $html = $this->getPDFHeader($title);
        $html .= $this->getFiltersSummary($filters);
        
        // Check if data exists
        if (empty($data)) {
            $html .= '<div style="text-align: center; padding: 20px; color: #777; font-size: 16px;">No low stock items found matching your criteria.</div>';
            return $this->generatePdf($html, 'low_stock_report.pdf');
        }
        
        // Create table
        $html .= '<table class="data-table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Material</th>';
        $html .= '<th>Category</th>';
        $html .= '<th>Warehouse</th>';
        $html .= '<th>Current Stock</th>';
        $html .= '<th>Minimum Stock</th>';
        $html .= '<th>Reorder Level</th>';
        $html .= '<th>Status</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        foreach ($data as $item) {
            $isCritical = $item['current_quantity'] <= $item['minimum_quantity'] / 2;
            $statusClass = $isCritical ? 'status-critical' : 'status-low';
            $statusText = $isCritical ? 'CRITICAL' : 'LOW';
            
            $html .= '<tr>';
            $html .= '<td>' . $item['name'] . ' (' . $item['item_code'] . ')</td>';
            $html .= '<td>' . $item['category_name'] . '</td>';
            $html .= '<td>' . ($item['warehouse_name'] ?? 'All Warehouses') . '</td>';
            $html .= '<td style="text-align: right;">' . $item['current_quantity'] . ' ' . $item['unit'] . '</td>';
            $html .= '<td style="text-align: right;">' . $item['minimum_quantity'] . ' ' . $item['unit'] . '</td>';
            $html .= '<td style="text-align: right;">' . $item['reorder_level'] . ' ' . $item['unit'] . '</td>';
            $html .= '<td class="' . $statusClass . '">' . $statusText . '</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= $this->getPDFFooter();
        
        return $this->generatePdf($html, 'low_stock_report.pdf');
    }
    
    /**
     * Get PDF header with company information and styling
     * 
     * @param string $title Report title
     * @return string HTML header content
     */
    private function getPDFHeader($title)
    {
        $companyName = $this->companyInfo['name'] ?? 'Construction Management System';
        $companyAddress = $this->companyInfo['address'] ?? 'Default Address';
        $date = date('Y-m-d H:i:s');
        
        $html = '<!DOCTYPE html>';
        $html .= '<html><head>';
        $html .= '<meta charset="UTF-8">';
        $html .= '<title>' . $title . '</title>';
        $html .= '<style>';
        $html .= $this->getPDFStyles();
        $html .= '</style>';
        $html .= '</head><body>';
        $html .= '<div class="header">';
        $html .= '<div class="company-info">';
        if (!empty($this->companyInfo['logo'])) {
            $html .= '<img src="' . $this->companyInfo['logo'] . '" alt="Company Logo" class="logo">';
        }
        $html .= '<div class="company-details">';
        $html .= '<h1>' . $companyName . '</h1>';
        $html .= '<p>' . $companyAddress . '</p>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="report-title">';
        $html .= '<h2>' . $title . '</h2>';
        $html .= '<p class="report-date">Generated on: ' . $date . '</p>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="content">';
        
        return $html;
    }
    
    /**
     * Get PDF footer
     * 
     * @return string HTML footer content
     */
    private function getPDFFooter()
    {
        $html = '</div>'; // Close content div
        $html .= '<div class="footer">';
        $html .= '<p>Inventory Management System</p>';
        $html .= '<script type="text/php">';
        $html .= 'if (isset($pdf)) {';
        $html .= '$font = $fontMetrics->get_font("DejaVu Sans", "normal");';
        $html .= '$size = 10;';
        $html .= '$color = array(0.3, 0.3, 0.3);';
        $html .= '$text_height = $fontMetrics->getFontHeight($font, $size);';
        $html .= '$w = $pdf->get_width();';
        $html .= '$h = $pdf->get_height();';
        $html .= '$y = $h - $text_height - 10;';
        $html .= '$pdf->page_text($w - 100, $y, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, $size, $color);';
        $html .= '}';
        $html .= '</script>';
        $html .= '</div>';
        $html .= '</body></html>';
        
        return $html;
    }
    
    /**
     * Get filters summary HTML
     * 
     * @param array $filters Report filters
     * @return string HTML filters summary
     */
    private function getFiltersSummary($filters)
    {
        if (empty($filters)) {
            return '';
        }
        
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
        if (!empty($filters['project_name'])) {
            $filterTexts[] = 'Project: ' . $filters['project_name'];
        }
        if (!empty($filters['category_id'])) {
            $filterTexts[] = 'Category: ' . $filters['category_name'];
        }
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $filterTexts[] = 'Date Range: ' . $filters['start_date'] . ' to ' . $filters['end_date'];
        }
        
        if (empty($filterTexts)) {
            return '';
        }
        
        $html = '<div class="filters-summary">';
        $html .= '<strong>Filters Applied:</strong> ' . implode(' | ', $filterTexts);
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Get PDF CSS styles
     * 
     * @return string CSS styles
     */
    private function getPDFStyles()
    {
        return '
            body {
                font-family: "DejaVu Sans", sans-serif;
                margin: 0;
                padding: 0;
                color: #333;
            }
            
            .header {
                border-bottom: 2px solid #333;
                padding-bottom: 20px;
                margin-bottom: 20px;
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
            }
            
            .company-info {
                display: flex;
                align-items: center;
            }
            
            .logo {
                max-height: 60px;
                margin-right: 20px;
            }
            
            .company-details h1 {
                margin: 0 0 5px 0;
                font-size: 20px;
                font-weight: bold;
            }
            
            .company-details p {
                margin: 0;
                font-size: 12px;
                color: #666;
            }
            
            .report-title {
                text-align: right;
            }
            
            .report-title h2 {
                margin: 0 0 5px 0;
                font-size: 18px;
                color: #333;
            }
            
            .report-date {
                margin: 0;
                font-size: 11px;
                color: #666;
            }
            
            .filters-summary {
                background-color: #f5f5f5;
                padding: 10px;
                margin-bottom: 20px;
                border-radius: 4px;
                font-size: 12px;
                border-left: 4px solid #007bff;
            }
            
            .content {
                margin-bottom: 30px;
            }
            
            .data-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 11px;
                margin-bottom: 20px;
            }
            
            .data-table th {
                background-color: #f8f9fa;
                border: 1px solid #dee2e6;
                padding: 8px;
                font-weight: bold;
                text-align: left;
                font-size: 12px;
            }
            
            .data-table td {
                border: 1px solid #dee2e6;
                padding: 8px;
                font-size: 11px;
            }
            
            .data-table tr:nth-child(even) {
                background-color: #f8f9fa;
            }
            
            .summary-row {
                background-color: #e9ecef !important;
                font-weight: bold;
                border-top: 2px solid #333;
            }
            
            .status-critical {
                color: #dc3545;
                font-weight: bold;
                background-color: #f8d7da;
                text-align: center;
            }
            
            .status-low {
                color: #856404;
                font-weight: bold;
                background-color: #fff3cd;
                text-align: center;
            }
            
            .footer {
                border-top: 1px solid #dee2e6;
                padding-top: 10px;
                margin-top: 30px;
                display: flex;
                justify-content: space-between;
                font-size: 10px;
                color: #666;
            }
            
            .page-number {
                margin: 0;
            }
        ';
    }
}