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
    
    public function generateStockValuationReport($data)
    {
        // Set title
        $title = 'Stock Valuation Report';
        
        // Start HTML content with CSS styling
        $html = $this->getPDFHeader($title);
        
        // Add detailed explanation section
        $html .= '<div class="explanation-section">';
        $html .= '<h3>Report Purpose & Methodology</h3>';
        $html .= '<div class="explanation-content">';
        $html .= '<p><strong>Purpose:</strong> This report provides a comprehensive valuation of all materials in stock across all warehouses, showing the total financial value of inventory assets.</p>';
        $html .= '<p><strong>Methodology:</strong> Valuation is calculated by multiplying the current stock quantity by the unit cost for each material. The unit cost represents the purchase price or standard cost of the material.</p>';
        $html .= '<p><strong>Key Metrics:</strong></p>';
        $html .= '<ul>';
        $html .= '<li><strong>Total Items:</strong> Count of unique materials in stock</li>';
        $html .= '<li><strong>Total Quantity:</strong> Sum of all material quantities across all warehouses</li>';
        $html .= '<li><strong>Total Value:</strong> Sum of all material values (quantity × unit cost)</li>';
        $html .= '<li><strong>Average Unit Cost:</strong> Weighted average cost per unit across all materials</li>';
        $html .= '</ul>';
        $html .= '<p><strong>Stock Status Indicators:</strong></p>';
        $html .= '<ul>';
        $html .= '<li><span class="status-normal">Normal</span>: Stock levels are adequate</li>';
        $html .= '<li><span class="status-low">Low Stock</span>: Stock at or below minimum reorder level</li>';
        $html .= '<li><span class="status-critical">Out of Stock</span>: No stock available</li>';
        $html .= '</ul>';
        $html .= '<p><strong>Report Generated:</strong> ' . date('F j, Y \a\t g:i A') . '</p>';
        $html .= '</div>';
        $html .= '</div>';
        
        // Check if data exists
        if (empty($data)) {
            $html .= '<div style="text-align: center; padding: 20px; color: #777; font-size: 16px;">No inventory data found for valuation.</div>';
            return $this->generatePdf($html, 'stock_valuation_report.pdf');
        }
        
        // Calculate totals
        $totalItems = count($data);
        $totalQuantity = array_sum(array_column($data, 'total_quantity'));
        $totalValue = array_sum(array_column($data, 'total_value'));
        $averageUnitCost = $totalQuantity > 0 ? $totalValue / $totalQuantity : 0;
        
        // Create summary section
        $html .= '<div class="summary-section">';
        $html .= '<h3>Valuation Summary</h3>';
        $html .= '<div class="summary-grid">';
        $html .= '<div class="summary-item"><strong>Total Items:</strong> ' . $totalItems . '</div>';
        $html .= '<div class="summary-item"><strong>Total Quantity:</strong> ' . number_format($totalQuantity, 2) . '</div>';
        $html .= '<div class="summary-item"><strong>Total Value:</strong> MWK ' . number_format($totalValue, 2) . '</div>';
        $html .= '<div class="summary-item"><strong>Average Unit Cost:</strong> MWK ' . number_format($averageUnitCost, 2) . '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        // Valuation by Warehouse
        $warehouseTotals = [];
        foreach ($data as $item) {
            $warehouseId = $item['warehouse_id'];
            if (!isset($warehouseTotals[$warehouseId])) {
                $warehouseTotals[$warehouseId] = [
                    'name' => $item['warehouse_name'],
                    'items' => 0,
                    'quantity' => 0,
                    'value' => 0
                ];
            }
            $warehouseTotals[$warehouseId]['items']++;
            $warehouseTotals[$warehouseId]['quantity'] += $item['total_quantity'];
            $warehouseTotals[$warehouseId]['value'] += $item['total_value'];
        }
        
        $html .= '<h3>Valuation by Warehouse</h3>';
        $html .= '<table class="data-table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Warehouse</th>';
        $html .= '<th>Items Count</th>';
        $html .= '<th>Total Quantity</th>';
        $html .= '<th>Total Value</th>';
        $html .= '<th>Percentage</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        foreach ($warehouseTotals as $warehouse) {
            $percentage = ($totalValue > 0) ? ($warehouse['value'] / $totalValue) * 100 : 0;
            $html .= '<tr>';
            $html .= '<td>' . $warehouse['name'] . '</td>';
            $html .= '<td style="text-align: right;">' . $warehouse['items'] . '</td>';
            $html .= '<td style="text-align: right;">' . number_format($warehouse['quantity'], 2) . '</td>';
            $html .= '<td style="text-align: right;">MWK ' . number_format($warehouse['value'], 2) . '</td>';
            $html .= '<td style="text-align: right;">' . number_format($percentage, 1) . '%</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody>';
        $html .= '</table>';
        
        // Material Valuation Details
        $html .= '<h3>Material Valuation Details</h3>';
        $html .= '<table class="data-table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>#</th>';
        $html .= '<th>Material</th>';
        $html .= '<th>Category</th>';
        $html .= '<th>Warehouse</th>';
        $html .= '<th>Unit Cost</th>';
        $html .= '<th>Quantity</th>';
        $html .= '<th>Total Value</th>';
        $html .= '<th>Stock Status</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        // Sort by total value descending
        usort($data, function($a, $b) {
            return $b['total_value'] - $a['total_value'];
        });
        
        $counter = 1;
        foreach ($data as $item) {
            $statusClass = 'status-normal';
            $statusText = 'Normal';
            
            if ($item['total_quantity'] == 0) {
                $statusClass = 'status-critical';
                $statusText = 'Out of Stock';
            } elseif ($item['total_quantity'] <= $item['minimum_quantity']) {
                $statusClass = 'status-low';
                $statusText = 'Low Stock';
            }
            
            $html .= '<tr>';
            $html .= '<td>' . $counter . '</td>';
            $html .= '<td>' . $item['material_name'] . ' (' . $item['item_code'] . ')</td>';
            $html .= '<td>' . ($item['category_name'] ?? 'Uncategorized') . '</td>';
            $html .= '<td>' . $item['warehouse_name'] . '</td>';
            $html .= '<td style="text-align: right;">MWK ' . number_format($item['unit_cost'], 2) . '</td>';
            $html .= '<td style="text-align: right;">' . number_format($item['total_quantity'], 2) . ' ' . $item['unit'] . '</td>';
            $html .= '<td style="text-align: right;">MWK ' . number_format($item['total_value'], 2) . '</td>';
            $html .= '<td class="' . $statusClass . '">' . $statusText . '</td>';
            $html .= '</tr>';
            
            $counter++;
        }
        
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= $this->getPDFFooter();
        
        return $this->generatePdf($html, 'stock_valuation_report.pdf');
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
            
            .explanation-section {
                background-color: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                padding: 15px;
                margin-bottom: 20px;
            }
            
            .explanation-section h3 {
                margin: 0 0 10px 0;
                font-size: 14px;
                color: #495057;
                border-bottom: 2px solid #007bff;
                padding-bottom: 5px;
            }
            
            .explanation-content {
                font-size: 11px;
                line-height: 1.4;
            }
            
            .explanation-content p {
                margin: 8px 0;
            }
            
            .explanation-content ul {
                margin: 8px 0 8px 20px;
                padding: 0;
            }
            
            .explanation-content li {
                margin: 4px 0;
            }
            
            .summary-section {
                background-color: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                padding: 15px;
                margin-bottom: 20px;
            }
            
            .summary-section h3 {
                margin: 0 0 15px 0;
                font-size: 14px;
                color: #495057;
            }
            
            .summary-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 10px;
            }
            
            .summary-item {
                background-color: #fff;
                padding: 10px;
                border-radius: 4px;
                border: 1px solid #dee2e6;
                font-size: 12px;
            }
            
            .summary-item strong {
                color: #495057;
                display: block;
                margin-bottom: 2px;
            }
        ';
    }
    
    /**
     * Generate supplier analysis report PDF
     * 
     * @param array $data Supplier analysis data
     * @param array $filters Report filters
     * @return string PDF output
     */
    public function generateSupplierAnalysisReport($data, $filters = [])
    {
        // Set title
        $title = 'Supplier Analysis Report';
        if (!empty($filters['supplier_name'])) {
            $title .= ' - ' . $filters['supplier_name'];
        }
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $title .= ' (' . $filters['start_date'] . ' to ' . $filters['end_date'] . ')';
        }
        
        // Start HTML content
        $html = $this->getPDFHeader($title);
        $html .= $this->getFiltersSummary($filters);
        
        // Check if data exists
        if (empty($data['suppliers'])) {
            $html .= '<div style="text-align: center; padding: 20px; color: #777; font-size: 16px;">No supplier data found matching your criteria.</div>';
            return $this->generatePdf($html, 'supplier_analysis_report.pdf');
        }
        
        // Calculate summary metrics
        $totalSuppliers = count($data['suppliers']);
        $activeSuppliers = count(array_filter($data['suppliers'], function($s) { return $s['status'] === 'active'; }));
        $totalMaterials = array_sum(array_column($data['suppliers'], 'material_count'));
        $avgRating = $totalSuppliers > 0 ? array_sum(array_column($data['suppliers'], 'rating')) / $totalSuppliers : 0;
        
        // Create summary section
        $html .= '<div class="summary-section">';
        $html .= '<h3>Supplier Overview</h3>';
        $html .= '<div class="summary-grid">';
        $html .= '<div class="summary-item"><strong>Total Suppliers:</strong> ' . $totalSuppliers . '</div>';
        $html .= '<div class="summary-item"><strong>Active Suppliers:</strong> ' . $activeSuppliers . '</div>';
        $html .= '<div class="summary-item"><strong>Total Materials:</strong> ' . $totalMaterials . '</div>';
        $html .= '<div class="summary-item"><strong>Avg. Rating:</strong> ' . number_format($avgRating, 1) . '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        // Supplier Status Distribution
        $statusCounts = [
            'active' => 0,
            'inactive' => 0,
            'pending' => 0
        ];
        
        foreach ($data['suppliers'] as $supplier) {
            if (isset($statusCounts[$supplier['status']])) {
                $statusCounts[$supplier['status']]++;
            }
        }
        
        $html .= '<h3>Supplier Status Distribution</h3>';
        $html .= '<table class="data-table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Status</th>';
        $html .= '<th>Count</th>';
        $html .= '<th>Percentage</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        foreach ($statusCounts as $status => $count) {
            $percentage = ($totalSuppliers > 0) ? ($count / $totalSuppliers) * 100 : 0;
            $html .= '<tr>';
            $html .= '<td>' . ucfirst($status) . '</td>';
            $html .= '<td style="text-align: right;">' . $count . '</td>';
            $html .= '<td style="text-align: right;">' . number_format($percentage, 1) . '%</td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody>';
        $html .= '</table>';
        
        // Supplier Details Table
        $html .= '<h3>Supplier Details</h3>';
        $html .= '<table class="data-table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>#</th>';
        $html .= '<th>Supplier Code</th>';
        $html .= '<th>Supplier Name</th>';
        $html .= '<th>Contact Person</th>';
        $html .= '<th>Email</th>';
        $html .= '<th>Phone</th>';
        $html .= '<th>Status</th>';
        $html .= '<th>Materials</th>';
        $html .= '<th>Rating</th>';
        $html .= '<th>Last Order</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        $counter = 1;
        foreach ($data['suppliers'] as $supplier) {
            $statusClass = 'status-normal';
            if ($supplier['status'] === 'inactive') {
                $statusClass = 'status-critical';
            } elseif ($supplier['status'] === 'pending') {
                $statusClass = 'status-low';
            }
            
            $html .= '<tr>';
            $html .= '<td>' . $counter . '</td>';
            $html .= '<td>' . $supplier['supplier_code'] . '</td>';
            $html .= '<td>' . $supplier['name'] . '</td>';
            $html .= '<td>' . $supplier['contact_person'] . '</td>';
            $html .= '<td>' . $supplier['email'] . '</td>';
            $html .= '<td>' . $supplier['phone'] . '</td>';
            $html .= '<td class="' . $statusClass . '">' . ucfirst($supplier['status']) . '</td>';
            $html .= '<td style="text-align: right;">' . $supplier['material_count'] . '</td>';
            $html .= '<td style="text-align: right;">' . ($supplier['rating'] ?? 'N/A') . '</td>';
            $html .= '<td>' . ($supplier['last_order_date'] ? date('Y-m-d', strtotime($supplier['last_order_date'])) : 'Never') . '</td>';
            $html .= '</tr>';
            
            $counter++;
        }
        
        $html .= '</tbody>';
        $html .= '</table>';
        
        // Material-Supplier Relationships
        if (!empty($data['supplierMaterials'])) {
            $html .= '<h3>Material-Supplier Relationships</h3>';
            $html .= '<table class="data-table">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th>#</th>';
            $html .= '<th>Material</th>';
            $html .= '<th>Supplier</th>';
            $html .= '<th>Unit Price</th>';
            $html .= '<th>Min Order Qty</th>';
            $html .= '<th>Lead Time</th>';
            $html .= '<th>Notes</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
            
            $counter = 1;
            foreach ($data['supplierMaterials'] as $relationship) {
                $html .= '<tr>';
                $html .= '<td>' . $counter . '</td>';
                $html .= '<td>' . $relationship['name'] . ' (' . $relationship['item_code'] . ')</td>';
                $html .= '<td>' . $relationship['supplier_name'] . '</td>';
                $html .= '<td style="text-align: right;">' . ($relationship['unit_price'] ? 'MWK ' . number_format($relationship['unit_price'], 2) : 'N/A') . '</td>';
                $html .= '<td style="text-align: right;">' . ($relationship['min_order_qty'] ? $relationship['min_order_qty'] . ' ' . $relationship['unit'] : 'N/A') . '</td>';
                $html .= '<td style="text-align: right;">' . ($relationship['lead_time'] ? $relationship['lead_time'] . ' days' : 'N/A') . '</td>';
                $html .= '<td>' . ($relationship['notes'] ?? 'N/A') . '</td>';
                $html .= '</tr>';
                
                $counter++;
            }
            
            $html .= '</tbody>';
            $html .= '</table>';
        }
        
        // Supplier Performance Summary
        $html .= '<h3>Supplier Performance Summary</h3>';
        
        // Top suppliers by material count
        $suppliersCopy = $data['suppliers'];
        usort($suppliersCopy, function($a, $b) {
            return ($b['material_count'] ?? 0) - ($a['material_count'] ?? 0);
        });
        $topSuppliers = array_slice($suppliersCopy, 0, 5);
        
        $html .= '<h4>Top Suppliers by Material Count</h4>';
        $html .= '<table class="data-table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Rank</th>';
        $html .= '<th>Supplier</th>';
        $html .= '<th>Supplier Code</th>';
        $html .= '<th>Material Count</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        $rank = 1;
        foreach ($topSuppliers as $supplier) {
            $html .= '<tr>';
            $html .= '<td>' . $rank . '</td>';
            $html .= '<td>' . $supplier['name'] . '</td>';
            $html .= '<td>' . $supplier['supplier_code'] . '</td>';
            $html .= '<td style="text-align: right;">' . $supplier['material_count'] . '</td>';
            $html .= '</tr>';
            $rank++;
        }
        
        $html .= '</tbody>';
        $html .= '</table>';
        
        // Top rated suppliers
        $suppliersCopy2 = $data['suppliers'];
        usort($suppliersCopy2, function($a, $b) {
            return ($b['rating'] ?? 0) - ($a['rating'] ?? 0);
        });
        $topRated = array_slice($suppliersCopy2, 0, 5);
        
        $html .= '<h4>Top Rated Suppliers</h4>';
        $html .= '<table class="data-table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Rank</th>';
        $html .= '<th>Supplier</th>';
        $html .= '<th>Supplier Type</th>';
        $html .= '<th>Rating</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        $rank = 1;
        foreach ($topRated as $supplier) {
            $html .= '<tr>';
            $html .= '<td>' . $rank . '</td>';
            $html .= '<td>' . $supplier['name'] . '</td>';
            $html .= '<td>' . $supplier['supplier_type'] . '</td>';
            $html .= '<td style="text-align: right;">' . ($supplier['rating'] ?? 'N/A') . '</td>';
            $html .= '</tr>';
            $rank++;
        }
        
        $html .= '</tbody>';
        $html .= '</table>';
        
        $html .= $this->getPDFFooter();
        
        return $this->generatePdf($html, 'supplier_analysis_report.pdf');
    }
}
