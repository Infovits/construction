<?php
namespace App\Libraries;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ExcelExport
{
    protected $data;
    protected $title;

    public function __construct($data, $title = 'Report')
    {
        $this->data = $data;
        $this->title = $title;
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($this->title, 0, 31)); // Max 31 characters for sheet title

        // Set document properties for better compatibility
        $spreadsheet->getProperties()
            ->setCreator('Construction Management System')
            ->setLastModifiedBy('Construction Management System')
            ->setTitle($this->title)
            ->setSubject($this->title)
            ->setDescription('Generated Excel Report')
            ->setKeywords('report excel')
            ->setCategory('Report');

        // Add column titles as the first row
        $column = 'A';
        if (!empty($this->data)) {
            foreach ($this->data[0] as $field => $value) {
                // Use proper column headers instead of raw field names
                $header = $this->getProperHeader($field);
                $sheet->setCellValue($column . '1', $header);
                $column++;
            }
        }

        // Apply formatting to header
        $lastColumn = '--'; // Initialize with a value that will be updated
        $columns = ['A'];
        for ($i = 'A'; $i < $column; $i++) {
            $lastColumn = $i;
            $columns[] = $i;
        }
        array_pop($columns); // Remove the last one which is past the data
        
        // Style header row
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'name' => 'Arial',
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Set column widths for better appearance
        foreach ($columns as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
            // Set minimum width
            $sheet->getColumnDimension($col)->setWidth(12);
        }

        // Add data to the sheet
        $row = 2;
        foreach ($this->data as $record) {
            $column = 'A';
            foreach ($record as $value) {
                // Ensure proper data type handling
                if (is_numeric($value)) {
                    $sheet->setCellValueExplicit($column . $row, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                } else {
                    $sheet->setCellValueExplicit($column . $row, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                }
                $column++;
            }
            $row++;
        }
        
        // Style the data rows
        $sheet->getStyle('A2:' . $lastColumn . ($row - 1))->applyFromArray([
            'font' => [
                'name' => 'Arial',
                'size' => 9,
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);
        
        // Set row heights for better appearance
        for ($r = 1; $r < $row; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(-1);
        }

        // Create a writer and save to a temporary file
        $writer = new Xlsx($spreadsheet);
        $tempFile = sys_get_temp_dir() . '/' . uniqid('excel_export_') . '.xlsx';
        
        try {
            // Set writer options for better compatibility
            $writer->setPreCalculateFormulas(false);
            $writer->save($tempFile);
            $fileContent = file_get_contents($tempFile);
            unlink($tempFile); // Clean up temp file
            return $fileContent;
        } catch (\Exception $e) {
            // Fallback to direct output if temp file fails
            ob_start();
            $writer->save('php://output');
            $fileContent = ob_get_clean();
            return $fileContent;
        }
    }
    
    /**
     * Export stock movement report
     * 
     * @param array $data The stock movement data
     * @return string The Excel file content
     */
    public function exportStockMovement($data)
    {
        $reportData = [];
        
        // Prepare data for Excel
        foreach ($data as $movement) {
            $reportData[] = [
                'Date' => date('Y-m-d', strtotime($movement['created_at'])),
                'Time' => date('H:i', strtotime($movement['created_at'])),
                'Material Code' => $movement['item_code'] ?? 'N/A',
                'Material Name' => $movement['material_name'] ?? $movement['name'] ?? 'Unknown',
                'Warehouse' => $movement['warehouse_name'] ?? 'N/A',
                'Movement Type' => $movement['movement_type'] ?? 'N/A',
                'Quantity' => ($movement['quantity'] ?? 0) . ' ' . ($movement['unit'] ?? ''),
                'Project' => $movement['project_name'] ?? 'N/A',
                'Recorded By' => $movement['performed_by_name'] ?? 'N/A'
            ];
        }
        
        if (empty($reportData)) {
            $reportData[] = [
                'Date' => '', 'Time' => '', 'Material Code' => '', 
                'Material Name' => '', 'Warehouse' => '', 'Movement Type' => '', 
                'Quantity' => '', 'Project' => '', 'Recorded By' => ''
            ];
        }
        
        $this->data = $reportData;
        $this->title = 'Stock Movement Report';
        
        return $this->export();
    }
    
    /**
     * Export project usage report
     * 
     * @param array $data The project usage data
     * @return string The Excel file content
     */
    public function exportProjectUsage($data)
    {
        $reportData = [];
        
        // Prepare data for Excel
        foreach ($data as $usage) {
            $itemCost = $usage['unit_cost'] * $usage['quantity'];
            $reportData[] = [
                'Date' => date('Y-m-d', strtotime($usage['created_at'])),
                'Project' => $usage['project_name'] ?? 'N/A',
                'Material Code' => $usage['item_code'] ?? 'N/A',
                'Material Name' => $usage['name'] ?? 'Unknown',
                'Category' => $usage['category_name'] ?? 'N/A',
                'Quantity Used' => $usage['quantity'] . ' ' . $usage['unit'],
                'Unit Cost' => number_format($usage['unit_cost'], 2),
                'Total Cost' => number_format($itemCost, 2),
                'Recorded By' => $usage['performed_by_name'] ?? 'N/A'
            ];
        }
        
        if (empty($reportData)) {
            $reportData[] = [
                'Date' => '', 'Project' => '', 'Material Code' => '', 
                'Material Name' => '', 'Category' => '', 'Quantity Used' => '', 
                'Unit Cost' => '', 'Total Cost' => '', 'Recorded By' => ''
            ];
        }
        
        $this->data = $reportData;
        $this->title = 'Project Usage Report';
        
        return $this->export();
    }
    
    /**
     * Export low stock report
     * 
     * @param array $data The low stock data
     * @return string The Excel file content
     */
    public function exportLowStock($data)
    {
        $reportData = [];
        
        // Prepare data for Excel
        foreach ($data as $item) {
            $reportData[] = [
                'Material Code' => $item['item_code'],
                'Material Name' => $item['name'],
                'Category' => $item['category_name'],
                'Warehouse' => $item['warehouse_name'] ?? 'All Warehouses',
                'Current Stock' => $item['current_quantity'] . ' ' . $item['unit'],
                'Minimum Stock' => $item['minimum_quantity'] . ' ' . $item['unit'],
                'Reorder Level' => $item['reorder_level'] . ' ' . $item['unit'],
                'Status' => ($item['current_quantity'] <= $item['minimum_quantity'] / 2) ? 'Critical' : 'Low',
                'Last Updated' => date('Y-m-d H:i', strtotime($item['updated_at']))
            ];
        }
        
        if (empty($reportData)) {
            $reportData[] = [
                'Material Code' => '', 'Material Name' => '', 'Category' => '', 
                'Warehouse' => '', 'Current Stock' => '', 'Minimum Stock' => '', 
                'Reorder Level' => '', 'Status' => '', 'Last Updated' => ''
            ];
        }
        
        $this->data = $reportData;
        $this->title = 'Low Stock Report';
        
        return $this->export();
    }
    
    /**
     * Export cost trend report
     * 
     * @param array $data The cost trend data
     * @return string The Excel file content
     */
    public function exportCostTrend($data)
    {
        $reportData = [];
        
        // Prepare data for Excel
        foreach ($data as $trend) {
            $reportData[] = [
                'Material Code' => $trend['item_code'],
                'Material Name' => $trend['name'],
                'Category' => $trend['category_name'],
                'Previous Cost' => number_format($trend['previous_cost'], 2),
                'Current Cost' => number_format($trend['current_cost'], 2),
                'Change %' => number_format($trend['change_percent'], 2) . '%',
                'Changed On' => date('Y-m-d', strtotime($trend['cost_change_date'])),
                'Supplier' => $trend['supplier_name'] ?? 'N/A'
            ];
        }
        
        if (empty($reportData)) {
            $reportData[] = [
                'Material Code' => '', 'Material Name' => '', 'Category' => '', 
                'Previous Cost' => '', 'Current Cost' => '', 'Change %' => '', 
                'Changed On' => '', 'Supplier' => ''
            ];
        }
        
        $this->data = $reportData;
        $this->title = 'Cost Trend Report';
        
        return $this->export();
    }
    
    /**
     * Export stock valuation report
     * 
     * @param array $data The stock valuation data
     * @return string The Excel file content
     */
    public function exportStockValuation($data)
    {
        $reportData = [];
        
        // Calculate totals for summary
        $totalItems = count($data);
        $totalQuantity = array_sum(array_column($data, 'total_quantity'));
        $totalValue = array_sum(array_column($data, 'total_value'));
        $averageUnitCost = $totalQuantity > 0 ? $totalValue / $totalQuantity : 0;
        
        // Add summary section
        $reportData[] = [
            'Summary' => 'Valuation Summary',
            'Total Items' => $totalItems,
            'Total Quantity' => number_format($totalQuantity, 2),
            'Total Value' => 'MWK ' . number_format($totalValue, 2),
            'Average Unit Cost' => 'MWK ' . number_format($averageUnitCost, 2)
        ];
        
        // Add blank row
        $reportData[] = [
            'Summary' => '', 'Total Items' => '', 'Total Quantity' => '', 
            'Total Value' => '', 'Average Unit Cost' => ''
        ];
        
        // Add warehouse totals
        $warehouseTotals = [];
        foreach ($data as $item) {
            // Use warehouse_name as the key since warehouse_id might not be available
            $warehouseKey = $item['warehouse_name'] ?? 'Unknown Warehouse';
            if (!isset($warehouseTotals[$warehouseKey])) {
                $warehouseTotals[$warehouseKey] = [
                    'name' => $item['warehouse_name'] ?? 'Unknown Warehouse',
                    'items' => 0,
                    'quantity' => 0,
                    'value' => 0
                ];
            }
            $warehouseTotals[$warehouseKey]['items']++;
            $warehouseTotals[$warehouseKey]['quantity'] += $item['total_quantity'];
            $warehouseTotals[$warehouseKey]['value'] += $item['total_value'];
        }
        
        $reportData[] = ['Summary' => 'Valuation by Warehouse'];
        $reportData[] = [
            'Summary' => 'Warehouse', 'Total Items' => 'Items Count', 
            'Total Quantity' => 'Total Quantity', 'Total Value' => 'Total Value', 
            'Average Unit Cost' => 'Percentage'
        ];
        
        foreach ($warehouseTotals as $warehouse) {
            $percentage = ($totalValue > 0) ? ($warehouse['value'] / $totalValue) * 100 : 0;
            $reportData[] = [
                'Summary' => $warehouse['name'],
                'Total Items' => $warehouse['items'],
                'Total Quantity' => number_format($warehouse['quantity'], 2),
                'Total Value' => 'MWK ' . number_format($warehouse['value'], 2),
                'Average Unit Cost' => number_format($percentage, 1) . '%'
            ];
        }
        
        // Add blank row
        $reportData[] = [
            'Summary' => '', 'Total Items' => '', 'Total Quantity' => '', 
            'Total Value' => '', 'Average Unit Cost' => ''
        ];
        
        // Add material details
        $reportData[] = ['Summary' => 'Material Valuation Details'];
        $reportData[] = [
            'Summary' => '#', 'Total Items' => 'Material', 
            'Total Quantity' => 'Category', 'Total Value' => 'Warehouse', 
            'Average Unit Cost' => 'Unit Cost'
        ];
        
        // Sort by total value descending
        usort($data, function($a, $b) {
            return $b['total_value'] - $a['total_value'];
        });
        
        $counter = 1;
        foreach ($data as $item) {
            $statusText = 'Normal';
            if ($item['total_quantity'] == 0) {
                $statusText = 'Out of Stock';
            } elseif ($item['total_quantity'] <= $item['minimum_quantity']) {
                $statusText = 'Low Stock';
            }
            
            $reportData[] = [
                'Summary' => $counter,
                'Total Items' => $item['material_name'] . ' (' . $item['item_code'] . ')',
                'Total Quantity' => $item['category_name'] ?? 'Uncategorized',
                'Total Value' => $item['warehouse_name'],
                'Average Unit Cost' => 'MWK ' . number_format($item['unit_cost'], 2)
            ];
            
            $counter++;
        }
        
        $this->data = $reportData;
        $this->title = 'Stock Valuation Report';
        
        return $this->export();
    }
    
    /**
     * Export supplier analysis report
     * 
     * @param array $data The supplier analysis data
     * @return string The Excel file content
     */
    public function exportSupplierAnalysis($data)
    {
        $reportData = [];
        
        // Calculate summary metrics
        $totalSuppliers = count($data['suppliers']);
        $activeSuppliers = count(array_filter($data['suppliers'], function($s) { return $s['status'] === 'active'; }));
        $totalMaterials = array_sum(array_column($data['suppliers'], 'material_count'));
        $avgRating = $totalSuppliers > 0 ? array_sum(array_column($data['suppliers'], 'rating')) / $totalSuppliers : 0;
        
        // Add summary section
        $reportData[] = [
            'Summary' => 'Supplier Overview',
            'Total Suppliers' => '',
            'Active Suppliers' => '',
            'Total Materials' => '',
            'Average Rating' => ''
        ];
        
        $reportData[] = [
            'Summary' => 'Total Suppliers',
            'Total Suppliers' => $totalSuppliers,
            'Active Suppliers' => $activeSuppliers,
            'Total Materials' => $totalMaterials,
            'Average Rating' => number_format($avgRating, 1)
        ];
        
        // Add blank row
        $reportData[] = [
            'Summary' => '', 'Total Suppliers' => '', 'Active Suppliers' => '', 
            'Total Materials' => '', 'Average Rating' => ''
        ];
        
        // Add supplier status distribution
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
        
        $reportData[] = ['Summary' => 'Supplier Status Distribution'];
        $reportData[] = [
            'Summary' => 'Status', 'Total Suppliers' => 'Count', 
            'Active Suppliers' => 'Percentage', 'Total Materials' => '', 
            'Average Rating' => ''
        ];
        
        foreach ($statusCounts as $status => $count) {
            $percentage = ($totalSuppliers > 0) ? ($count / $totalSuppliers) * 100 : 0;
            $reportData[] = [
                'Summary' => ucfirst($status),
                'Total Suppliers' => $count,
                'Active Suppliers' => number_format($percentage, 1) . '%',
                'Total Materials' => '',
                'Average Rating' => ''
            ];
        }
        
        // Add blank row
        $reportData[] = [
            'Summary' => '', 'Total Suppliers' => '', 'Active Suppliers' => '', 
            'Total Materials' => '', 'Average Rating' => ''
        ];
        
        // Add supplier details
        $reportData[] = ['Summary' => 'Supplier Details'];
        $reportData[] = [
            'Summary' => '#', 'Total Suppliers' => 'Supplier Code', 
            'Active Suppliers' => 'Supplier Name', 'Total Materials' => 'Contact Person', 
            'Average Rating' => 'Email'
        ];
        
        $counter = 1;
        foreach ($data['suppliers'] as $supplier) {
            $reportData[] = [
                'Summary' => $counter,
                'Total Suppliers' => $supplier['supplier_code'],
                'Active Suppliers' => $supplier['name'],
                'Total Materials' => $supplier['contact_person'],
                'Average Rating' => $supplier['email']
            ];
            
            $counter++;
        }
        
        // Add blank row
        $reportData[] = [
            'Summary' => '', 'Total Suppliers' => '', 'Active Suppliers' => '', 
            'Total Materials' => '', 'Average Rating' => ''
        ];
        
        // Add material-supplier relationships if they exist
        if (!empty($data['supplierMaterials'])) {
            $reportData[] = ['Summary' => 'Material-Supplier Relationships'];
            $reportData[] = [
                'Summary' => '#', 'Total Suppliers' => 'Material', 
                'Active Suppliers' => 'Supplier', 'Total Materials' => 'Unit Price', 
                'Average Rating' => 'Min Order Qty'
            ];
            
            $counter = 1;
            foreach ($data['supplierMaterials'] as $relationship) {
                $reportData[] = [
                    'Summary' => $counter,
                    'Total Suppliers' => $relationship['name'] . ' (' . $relationship['item_code'] . ')',
                    'Active Suppliers' => $relationship['supplier_name'],
                    'Total Materials' => ($relationship['unit_price'] ? 'MWK ' . number_format($relationship['unit_price'], 2) : 'N/A'),
                    'Average Rating' => ($relationship['min_order_qty'] ? $relationship['min_order_qty'] . ' ' . $relationship['unit'] : 'N/A')
                ];
                
                $counter++;
            }
        }
        
        $this->data = $reportData;
        $this->title = 'Supplier Analysis Report';
        
        return $this->export();
    }
    
    /**
     * Get proper column header for a field name
     * 
     * @param string $field The raw field name
     * @return string The proper column header
     */
    private function getProperHeader($field)
    {
        $headerMap = [
            'Summary' => 'Summary',
            'Total Items' => 'Total Items',
            'Total Quantity' => 'Total Quantity',
            'Total Value' => 'Total Value',
            'Average Unit Cost' => 'Average Unit Cost',
            'Material ID' => 'Material ID',
            'Material Name' => 'Material Name',
            'Item Code' => 'Item Code',
            'Category ID' => 'Category ID',
            'Category Name' => 'Category Name',
            'Warehouse ID' => 'Warehouse ID',
            'Warehouse Name' => 'Warehouse Name',
            'Unit' => 'Unit',
            'Unit Cost' => 'Unit Cost',
            'Total Quantity' => 'Total Quantity',
            'Total Value' => 'Total Value',
            'Minimum Quantity' => 'Minimum Quantity',
            'Date' => 'Date',
            'Time' => 'Time',
            'Warehouse' => 'Warehouse',
            'Movement Type' => 'Movement Type',
            'Quantity' => 'Quantity',
            'Project' => 'Project',
            'Recorded By' => 'Recorded By',
            'Project' => 'Project',
            'Category' => 'Category',
            'Quantity Used' => 'Quantity Used',
            'Unit Cost' => 'Unit Cost',
            'Total Cost' => 'Total Cost',
            'Material Code' => 'Material Code',
            'Current Stock' => 'Current Stock',
            'Minimum Stock' => 'Minimum Stock',
            'Reorder Level' => 'Reorder Level',
            'Status' => 'Status',
            'Last Updated' => 'Last Updated',
            'Previous Cost' => 'Previous Cost',
            'Current Cost' => 'Current Cost',
            'Change %' => 'Change %',
            'Changed On' => 'Changed On',
            'Supplier' => 'Supplier',
            'Items Count' => 'Items Count',
            'Percentage' => 'Percentage'
        ];
        
        return $headerMap[$field] ?? $field;
    }
}
