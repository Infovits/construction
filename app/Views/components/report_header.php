<?php
/**
 * Report Header Component
 * Include this in all PDF report views for consistent company branding
 */
?>

<style>
    @page {
        margin: 20mm 15mm 20mm 15mm;
        header: html_header;
        footer: html_footer;
    }
    
    body {
        font-family: 'Segoe UI', 'Helvetica', 'Arial', sans-serif;
        font-size: 11px;
        line-height: 1.6;
        color: #333;
        margin: 0;
        padding: 0;
        background: #ffffff;
    }
    
    /* Company Header */
    .company-header {
        display: flex;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 3px solid #2c3e50;
    }
    
    .logo-container {
        flex-shrink: 0;
        width: 120px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }
    
    .company-logo {
        max-width: 110px;
        max-height: 70px;
        object-fit: contain;
    }
    
    .company-info {
        flex: 1;
    }
    
    .company-name {
        font-size: 24px;
        font-weight: 700;
        color: #2c3e50;
        margin: 0 0 5px 0;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .company-detail {
        font-size: 11px;
        color: #555;
        margin: 3px 0;
        line-height: 1.4;
    }
    
    .report-info {
        margin-top: 20px;
    }
    
    .report-title {
        font-size: 18px;
        font-weight: 700;
        color: #333;
        margin: 10px 0 5px 0;
        text-transform: uppercase;
    }
    
    .report-meta {
        display: flex;
        justify-content: space-between;
        font-size: 10px;
        color: #666;
        margin-top: 5px;
    }
    
    /* Report Table */
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
        padding: 12px;
        text-align: left;
        font-weight: 700;
        border: 1px solid #2c3e50;
    }
    
    .report-table td {
        padding: 10px 12px;
        border: 1px solid #dee2e6;
    }
    
    .report-table tbody tr:nth-child(even) {
        background: #f8f9fa;
    }
    
    .text-right {
        text-align: right;
    }
    
    .text-center {
        text-align: center;
    }
    
    .text-bold {
        font-weight: 700;
    }
    
    /* Sections */
    .report-section {
        margin-top: 25px;
        page-break-inside: avoid;
    }
    
    .section-title {
        font-size: 14px;
        font-weight: 700;
        color: #2c3e50;
        margin: 15px 0 10px 0;
        padding-bottom: 8px;
        border-bottom: 2px solid #dee2e6;
    }
    
    /* Summary Boxes */
    .summary-box {
        display: inline-block;
        min-width: 200px;
        margin: 10px 15px 10px 0;
        padding: 15px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }
    
    .summary-label {
        font-size: 10px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }
    
    .summary-value {
        font-size: 16px;
        font-weight: 700;
        color: #2c3e50;
    }
    
    /* Footer */
    .report-footer {
        margin-top: 40px;
        padding-top: 15px;
        border-top: 1px solid #dee2e6;
        font-size: 10px;
        color: #999;
        text-align: center;
    }
    
    .footer-text {
        margin: 3px 0;
    }
    
    .generated-date {
        font-size: 9px;
        color: #bbb;
        margin-top: 10px;
    }
</style>

<div class="company-header">
    <?php if (!empty($logo_url)): ?>
    <div class="logo-container">
        <img src="<?= htmlspecialchars($logo_url) ?>" alt="<?= htmlspecialchars($company_name ?? 'Company Logo') ?>" class="company-logo">
    </div>
    <?php endif; ?>
    
    <div class="company-info">
        <h1 class="company-name"><?= htmlspecialchars($company_name ?? 'Company') ?></h1>
        <?php if (!empty($company_address)): ?>
        <p class="company-detail"><?= htmlspecialchars($company_address) ?></p>
        <?php endif; ?>
        <?php if (!empty($company_phone)): ?>
        <p class="company-detail">Phone: <?= htmlspecialchars($company_phone) ?></p>
        <?php endif; ?>
        <?php if (!empty($company_email)): ?>
        <p class="company-detail">Email: <?= htmlspecialchars($company_email) ?></p>
        <?php endif; ?>
        <?php if (!empty($company_website)): ?>
        <p class="company-detail">Website: <?= htmlspecialchars($company_website) ?></p>
        <?php endif; ?>
    </div>
</div>
