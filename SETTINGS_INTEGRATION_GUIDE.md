# Settings System Integration - Complete Implementation Guide

## ✅ What Has Been Implemented

### 1. **Settings Helper Functions** (`app/Helpers/settings_helper.php`)
Created comprehensive helper functions for easy access to settings throughout the application:

- `get_setting($key, $default)` - Get any setting value
- `get_company_name()` - Get company name
- `get_company_logo()` - Get company logo URL
- `get_timezone()` - Get timezone
- `get_date_format()` - Get date format
- `get_currency()` - Get default currency
- `format_date($date, $format)` - Format dates using system settings
- `format_datetime($datetime)` - Format datetime using system settings
- `format_money($amount, $currency)` - Format currency using system settings
- `get_items_per_page()` - Get pagination setting
- `get_theme()` - Get theme preference
- `is_setting_enabled($key)` - Check boolean settings

### 2. **Company Logo Upload Feature**
- Added logo upload to General Settings
- Logo stored in `uploads/logos/` directory
- Automatic old logo deletion on new upload
- Option to remove logo
- Validation: max 2MB, image files only
- Supported formats: JPG, PNG, GIF

### 3. **Auto-loaded Settings Helper**
Updated `app/Config/Autoload.php` to automatically load settings helper on every request.

### 4. **Updated Layout**
- `app/Views/layouts/main.php` now uses:
  - `get_company_name()` for page title
  - `get_company_logo()` for sidebar logo
  - Dynamic company name display

### 5. **Currency Formatting Enhancement**
Updated `app/Helpers/utility_helper.php`:
- `format_currency()` now automatically uses system currency settings
- Supports multiple currency symbols (MWK, USD, EUR, GBP, ZAR)

### 6. **Timezone Support**
Updated `app/Controllers/BaseController.php`:
- Automatically sets PHP timezone based on system settings
- Applied to all controllers globally

### 7. **Enhanced General Settings View**
`app/Views/settings/general.php` now includes:
- Company logo upload with preview
- Timezone dropdown (15+ options)
- Date format dropdown with examples
- Currency dropdown (5 currencies)
- Required field validation

---

## 🎯 How to Use Settings in Your Code

### **Get Company Information**
```php
<?php
helper('settings'); // Load if not auto-loaded

// Get company name
echo get_company_name(); // "ABC Construction Ltd"

// Get company logo
echo '<img src="' . get_company_logo() . '" alt="Logo">';
```

### **Format Dates**
```php
// Use system date format
echo format_date('2026-02-03'); // Output based on settings: "03/02/2026" or "02/03/2026"

// Format datetime
echo format_datetime('2026-02-03 14:30:00'); // "03/02/2026 14:30:00"

// Custom format
echo format_date('2026-02-03', 'd-M-Y'); // "03-Feb-2026"
```

### **Format Currency**
```php
// Use system currency
echo format_money(1500.50); // "MK 1,500.50" (if MWK selected)
echo format_currency(1500.50); // Same as format_money

// Override currency
echo format_money(1500.50, 'USD'); // "$ 1,500.50"
```

### **Access Any Setting**
```php
// Get specific setting
$companyName = get_setting('general_company_name', 'Default Company');
$timeout = get_setting('security_session_timeout', 30);

// Check boolean settings
if (is_setting_enabled('security_two_factor')) {
    // 2FA is enabled
}

// Get pagination setting
$perPage = get_items_per_page(); // 25
```

---

## 📝 Views That Need Updates

### **Replace hardcoded date formatting:**

**BEFORE:**
```php
<?= date('M j, Y', strtotime($project['created_at'])) ?>
```

**AFTER:**
```php
<?= format_date($project['created_at']) ?>
```

### **Replace hardcoded currency:**

**BEFORE:**
```php
MWK <?= number_format($amount, 2) ?>
```

**AFTER:**
```php
<?= format_money($amount) ?>
```

### **Replace hardcoded company name:**

**BEFORE:**
```php
<h1>Helmet Construction</h1>
```

**AFTER:**
```php
<h1><?= get_company_name() ?></h1>
```

---

## 🔧 Settings Available

### **General Settings (`general_*`)**
- `company_name` - Company name
- `company_logo` - Logo file path
- `timezone` - PHP timezone (e.g., "Africa/Blantyre")
- `date_format` - PHP date format (Y-m-d, d/m/Y, m/d/Y)
- `currency` - Currency code (MWK, USD, EUR, GBP, ZAR)

### **Security Settings (`security_*`)**
- `password_policy` - standard/strong/very_strong
- `session_timeout` - Minutes (5-240)
- `two_factor` - Boolean (true/false)

### **Preferences (`preferences_*`)**
- `theme` - light/dark
- `items_per_page` - Number (10-100)
- `email_notifications` - Boolean

### **Integrations (`integrations_*`)**
- `webhook_url` - Webhook URL
- `slack_webhook` - Slack webhook URL

---

## 📂 Files Modified

1. ✅ `app/Helpers/settings_helper.php` - Created
2. ✅ `app/Views/settings/general.php` - Logo upload added
3. ✅ `app/Controllers/Settings.php` - Logo handling added
4. ✅ `app/Models/SettingModel.php` - Fixed created_at issue
5. ✅ `app/Config/Autoload.php` - Auto-load settings helper
6. ✅ `app/Views/layouts/main.php` - Use settings for title, logo, name
7. ✅ `app/Helpers/utility_helper.php` - Updated format_currency()
8. ✅ `app/Controllers/BaseController.php` - Auto-set timezone

---

## 🚀 Next Steps for Full Integration

### **1. Update Date Displays (30+ locations)**
Files needing updates:
- `app/Views/tasks/view.php` (6 locations)
- `app/Views/tasks/report.php` (2 locations)
- `app/Views/project_categories/view.php` (3 locations)
- `app/Views/accounting/**/*.php` (multiple)

### **2. Update Currency Displays (20+ locations)**
Files needing updates:
- `app/Views/tasks/view.php`
- `app/Views/accounting/journal_entries/create.php`
- All invoice/payment views
- All budget/financial views

### **3. Create Upload Directory**
```bash
mkdir -p public/uploads/logos
chmod 755 public/uploads/logos
```

### **4. Add Logo Placeholder**
Create `public/assets/images/logo-placeholder.png` (optional)

---

## 🎨 Logo Specifications

**Recommended:**
- Size: 200x200px (square)
- Format: PNG (transparent background)
- Max file size: 2MB
- Aspect ratio: 1:1

**Supported formats:**
- PNG, JPG, JPEG, GIF

**Storage location:**
- `public/uploads/logos/logo_[company_id]_[timestamp].ext`

---

## 💡 Examples of Settings in Action

### **Dashboard Header**
```php
<div class="header">
    <img src="<?= get_company_logo() ?>" alt="<?= get_company_name() ?>">
    <h1><?= get_company_name() ?></h1>
</div>
```

### **Invoice/Report**
```php
<div class="invoice-header">
    <img src="<?= get_company_logo() ?>" width="100">
    <h2><?= get_company_name() ?></h2>
    <p>Date: <?= format_date(date('Y-m-d')) ?></p>
    <p>Amount: <?= format_money($total) ?></p>
</div>
```

### **Data Table**
```php
<td><?= format_date($row['created_at']) ?></td>
<td><?= format_money($row['amount']) ?></td>
```

---

## ✨ Benefits

1. **Centralized Configuration**: All settings in one place
2. **Multi-tenancy Ready**: Settings per company
3. **Easy Updates**: Change once, affects entire system
4. **Type Safety**: Automatic type conversion
5. **Performance**: Built-in caching per request
6. **Flexibility**: Easy to add new settings
7. **Professional**: Company branding throughout system

---

## 🔐 Security Notes

- Logo uploads validated (type, size)
- Old logos automatically deleted
- Settings filtered by company_id
- Permission checks on settings edit
- XSS protection with esc() function

---

**System is now configured to use settings throughout!** 🎉

All new views should use these helper functions instead of hardcoded values.
