<?php

namespace App\Libraries;

/**
 * Report PDF Helper
 * Provides common functions for generating PDF reports with company branding
 */
class ReportPDFHelper
{
    /**
     * Get company header HTML for PDFs
     */
    public static function getCompanyHeader($company, $logoUrl = null): string
    {
        $html = '<div class="company-header">';
        
        if ($logoUrl) {
            $html .= '<div class="header-logo-container">';
            $html .= '<img src="' . esc($logoUrl) . '" alt="' . esc($company['name'] ?? 'Company Logo') . '" class="header-logo">';
            $html .= '</div>';
        }
        
        $html .= '<div class="header-info">';
        $html .= '<h1 class="company-name">' . esc($company['name'] ?? 'Company Name') . '</h1>';
        
        if (!empty($company['address'])) {
            $html .= '<p class="company-detail">' . esc($company['address']) . '</p>';
        }
        
        if (!empty($company['phone'])) {
            $html .= '<p class="company-detail">Phone: ' . esc($company['phone']) . '</p>';
        }
        
        if (!empty($company['email'])) {
            $html .= '<p class="company-detail">Email: ' . esc($company['email']) . '</p>';
        }
        
        if (!empty($company['website'])) {
            $html .= '<p class="company-detail">Website: ' . esc($company['website']) . '</p>';
        }
        
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Get common PDF styles for report headers
     */
    public static function getHeaderStyles(): string
    {
        return <<<CSS
            .company-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 30px;
                padding-bottom: 20px;
                border-bottom: 3px solid #2c3e50;
                gap: 20px;
            }
            
            .header-logo-container {
                flex-shrink: 0;
                width: 120px;
                height: 80px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 4px;
                padding: 5px;
            }
            
            .header-logo {
                max-width: 110px;
                max-height: 70px;
                object-fit: contain;
            }
            
            .header-info {
                flex-grow: 1;
            }
            
            .company-name {
                font-size: 24px;
                font-weight: 700;
                color: #2c3e50;
                margin: 0 0 8px 0;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            
            .company-detail {
                font-size: 11px;
                color: #555;
                margin: 4px 0;
                line-height: 1.4;
            }
            
            .report-meta {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
                font-size: 11px;
                color: #666;
            }
            
            .report-title {
                font-size: 18px;
                font-weight: 700;
                color: #333;
                margin-bottom: 10px;
                text-transform: uppercase;
            }
            
            .report-date {
                font-size: 10px;
                color: #999;
            }
        CSS;
    }

    /**
     * Get common table styles for reports
     */
    public static function getTableStyles(): string
    {
        return <<<CSS
            .report-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
                font-size: 11px;
            }
            
            .report-table thead {
                background: #2c3e50;
                color: white;
            }
            
            .report-table th {
                padding: 10px;
                text-align: left;
                font-weight: 700;
                border: 1px solid #2c3e50;
            }
            
            .report-table td {
                padding: 8px 10px;
                border: 1px solid #dee2e6;
            }
            
            .report-table tbody tr:nth-child(even) {
                background: #f8f9fa;
            }
            
            .report-table tbody tr:hover {
                background: #f0f0f0;
            }
            
            .text-right {
                text-align: right;
            }
            
            .text-center {
                text-align: center;
            }
        CSS;
    }

    /**
     * Get footer styles for reports
     */
    public static function getFooterStyles(): string
    {
        return <<<CSS
            .report-footer {
                margin-top: 30px;
                padding-top: 15px;
                border-top: 1px solid #dee2e6;
                font-size: 10px;
                color: #999;
                text-align: center;
            }
            
            .footer-text {
                margin: 5px 0;
            }
            
            .generated-date {
                font-size: 9px;
                color: #bbb;
                margin-top: 10px;
            }
        CSS;
    }

    /**
     * Get complete PDF styles combining header, table, and footer
     */
    public static function getAllStyles(): string
    {
        return self::getHeaderStyles() . "\n" . self::getTableStyles() . "\n" . self::getFooterStyles();
    }

    /**
     * Get report footer HTML
     */
    public static function getReportFooter($companyName = null): string
    {
        $html = '<div class="report-footer">';
        $html .= '<p class="footer-text">© ' . date('Y') . ' ' . esc($companyName ?? 'Company') . '. All rights reserved.</p>';
        $html .= '<p class="generated-date">Generated on: ' . date('F d, Y \a\t H:i A') . '</p>';
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Get report information summary
     */
    public static function getReportInfo($title, $dateRange = null, $department = null): string
    {
        $html = '<div class="report-meta">';
        $html .= '<div>';
        $html .= '<div class="report-title">' . esc($title) . '</div>';
        if ($dateRange) {
            $html .= '<p class="report-date">Period: ' . esc($dateRange) . '</p>';
        }
        if ($department) {
            $html .= '<p class="report-date">Department: ' . esc($department) . '</p>';
        }
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
}
