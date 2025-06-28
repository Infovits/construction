<?php
namespace App\Libraries;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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

        // Add column titles as the first row
        $column = 'A';
        foreach ($this->data[0] as $field => $value) {
            $sheet->setCellValue($column . '1', $field);
            $column++;
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
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Add data to the sheet
        $row = 2;
        foreach ($this->data as $record) {
            $column = 'A';
            foreach ($record as $value) {
                $sheet->setCellValue($column . $row, $value);
                $column++;
            }
            $row++;
        }
        
        // Style the data rows
        $sheet->getStyle('A2:' . $lastColumn . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);
        
        // Auto-size columns
        foreach ($columns as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create a writer and get the file content
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $fileContent = ob_get_clean();

        return $fileContent;
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
                'Material Code' => $movement['item_code'],
                'Material Name' => $movement['name'],
                'Warehouse' => $movement['warehouse_name'],
                'Movement Type' => $movement['movement_type'],
                'Quantity' => $movement['quantity'] . ' ' . $movement['unit'],
                'Project' => $movement['project_name'] ?? 'N/A',
                'Recorded By' => $movement['first_name'] . ' ' . $movement['last_name']
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
            $reportData[] = [
                'Date' => date('Y-m-d', strtotime($usage['created_at'])),
                'Project' => $usage['project_name'],
                'Material Code' => $usage['item_code'],
                'Material Name' => $usage['name'],
                'Category' => $usage['category_name'],
                'Quantity Used' => $usage['quantity'] . ' ' . $usage['unit'],
                'Unit Cost' => number_format($usage['unit_cost'], 2),
                'Total Cost' => number_format($usage['unit_cost'] * $usage['quantity'], 2),
                'Recorded By' => $usage['first_name'] . ' ' . $usage['last_name']
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
}