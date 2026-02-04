# File Management & Incident & Safety Reporting Modules

## Overview

This document provides comprehensive documentation for the two new modules added to the Construction Management System:

1. **File Management Module** - Manage and organize project documents with version control
2. **Incident & Safety Reporting Module** - Track incidents, audits, and safety performance

---

## 1. FILE MANAGEMENT MODULE

### Features

- **Upload and organize files** by project and category
- **Document preview and version history** - Track all file changes
- **Access controls** - Manage view/edit/delete permissions per user
- **Search and tagging support** - Find files quickly with advanced search
- **Commenting on files** - Collaborate with team on documents
- **Change tracking** - Complete audit trail of all file actions

### Database Tables

#### file_categories
- Organize files into categories (e.g., Blueprints, Permits, Contracts, Reports)
- Color-coded for easy visual identification

#### files
- Main table storing file metadata
- Tracks file path, size, type, upload date, expiration
- Version control support with `version_number` and `is_latest_version`

#### file_versions
- Maintains complete version history
- Stores path and metadata for each version
- Allows comparison and rollback to previous versions

#### file_access_controls
- Granular permission management
- Access types: view, edit, delete, manage
- Supports expirable access (time-limited sharing)

#### file_tags
- Multi-tagging support for flexible organization
- Search files by tags

#### file_comments
- Collaborative commenting on files
- Mark comments as resolved
- Track comment threads

#### file_change_logs
- Complete audit trail of all file actions
- Tracks uploads, updates, deletions, comments, shares
- Records IP address and timestamp

### Routes

```
GET/POST  /file-management                    - View files list
POST      /file-management/upload              - Upload new file(s)
GET       /file-management/view/:id            - View file details
GET       /file-management/download/:id        - Download file
POST      /file-management/delete/:id          - Archive/delete file
POST      /file-management/updateVersion/:id   - Update file version
POST      /file-management/comment/:id         - Add comment to file
GET       /file-management/search              - Search files
GET       /file-management/category/:id        - View files by category
POST      /file-management/grantAccess/:id     - Share file with user
```

### Models

- `FileModel` - File management operations
- `FileVersionModel` - Version history
- `FileCategoryModel` - Categories management
- `FileAccessControlModel` - Permission management
- `FileTagModel` - Tags management
- `FileCommentModel` - Comments management
- `FileChangeLogModel` - Audit trail

### Usage Examples

**Upload Files:**
```php
// Upload files via FileManagement controller
POST /file-management/upload
- project_id (required)
- files[] (required, array of files)
- category_id (optional)
- description (optional)
- tags (optional, comma-separated)
- document_date (optional)
- expires_at (optional)
```

**Search Files:**
```php
GET /file-management/search?project_id=1&q=blueprint
```

**Grant Access:**
```php
POST /file-management/grantAccess/5
- user_id (required)
- access_type (required: view, edit, delete, manage)
- expires_at (optional)
```

---

## 2. INCIDENT & SAFETY REPORTING MODULE

### Features

- **Log incidents** with photos, notes, and affected people
- **Track resolution** with action steps and assignment
- **Upload safety audits and reports** - Store audit documents and findings
- **Generate safety trend analytics** - Visualize safety performance over time
- **Filter incidents** by type, severity, project, and status
- **Safety audit management** - Record and track audit findings
- **Generate reports** - Daily, weekly, monthly, quarterly, annual safety reports

### Database Tables

#### incident_severity_levels
- Define severity levels (Critical, High, Medium, Low)
- Color-coded for easy identification
- Configurable for each company

#### incident_types
- Injury, Equipment Damage, Near Miss, Environmental, Safety Violation, etc.
- Customizable incident classifications

#### incidents
- Main incident table
- Stores incident details, affected people, witnesses
- Tracks status: reported → investigating → resolved → closed
- Automatic incident code generation

#### incident_photos
- Store photos as evidence
- Support for before/after/evidence/overview photos
- Track uploader and timestamp

#### incident_action_steps
- Track required corrective actions
- Assign to users with due dates
- Mark as pending, in-progress, completed, or overdue
- Support critical action flags

#### safety_audits
- Record safety audit details
- Track conformance percentage
- Link to incidents if required
- Store document path for audit reports

#### safety_audit_findings
- Detailed findings from audits
- Severity levels (critical, major, minor)
- Track corrective actions and closure

#### safety_analytics
- Daily safety metrics and trends
- Track incidents by severity
- Calculate trending (improving, stable, declining)
- Monthly comparison data

#### safety_reports
- Generate periodic safety reports
- Daily, weekly, monthly, quarterly, annual
- Track approval workflow
- Distribution list management

### Routes

```
-- Dashboard
GET       /incident-safety/dashboard           - Safety overview

-- Incidents
GET       /incident-safety/incidents           - List incidents with filters
GET       /incident-safety/incidents/create    - Create incident form
POST      /incident-safety/incidents/store     - Store new incident
GET       /incident-safety/incidents/:id       - View incident details
POST      /incident-safety/incidents/:id/status - Update incident status
POST      /incident-safety/incidents/:id/photos - Upload incident photos
POST      /incident-safety/incidents/:id/action-steps - Add action step
POST      /incident-safety/action-steps/:id/complete - Mark action complete

-- Safety Audits
GET       /incident-safety/audits              - List audits
GET       /incident-safety/audits/create       - Create audit form
POST      /incident-safety/audits/store        - Store new audit
GET       /incident-safety/audits/:id          - View audit details

-- Safety Reports
GET       /incident-safety/reports             - List reports
GET       /incident-safety/reports/create      - Create report form
POST      /incident-safety/reports/store       - Store new report
GET       /incident-safety/reports/:id         - View report details

-- Analytics
GET       /incident-safety/analytics           - Safety trend analytics
```

### Models

- `IncidentModel` - Incident management
- `IncidentTypeModel` - Incident types
- `IncidentSeverityModel` - Severity levels
- `IncidentPhotoModel` - Incident photos
- `IncidentActionStepModel` - Action steps tracking
- `SafetyAuditModel` - Audit management
- `SafetyAnalyticsModel` - Trends and analytics
- `SafetyReportModel` - Report generation

### Controllers

- `IncidentSafety` - Main controller for all incident and safety operations

### Usage Examples

**Report Incident:**
```php
POST /incident-safety/incidents/store
- project_id (required)
- incident_type_id (required)
- severity_id (required)
- title (required)
- description (required)
- incident_date (required)
- incident_time (optional)
- location (optional)
- affected_people_count (optional)
- affected_people_names (optional)
- witness_count (optional)
- witness_names (optional)
- injuries_sustained (optional)
- property_damage_description (optional)
- immediate_actions_taken (optional)
- photos[] (optional)
```

**Update Incident Status:**
```php
POST /incident-safety/incidents/5/status
- status (required: reported, investigating, resolved, closed)
- notes (optional, for findings)
```

**Add Action Step:**
```php
POST /incident-safety/incidents/5/action-steps
- action_description (required)
- assigned_to (optional)
- due_date (optional)
- is_critical (optional, 0 or 1)
```

**Create Safety Audit:**
```php
POST /incident-safety/audits/store
- project_id (required)
- audit_date (required)
- audit_type (required: routine, incident_related, compliance, follow_up)
- audit_scope (optional)
- total_observations (optional)
- conformance_percentage (optional)
```

**Generate Safety Report:**
```php
POST /incident-safety/reports/store
- project_id (required)
- report_type (required: daily, weekly, monthly, quarterly, annual)
- report_period_start (required)
- report_period_end (required)
- total_incidents_reported (optional)
- key_highlights (optional)
- challenges_identified (optional)
- recommendations (optional)
```

---

## 3. DATABASE SETUP

### Installation Steps

1. **Execute SQL migrations:**
   ```bash
   # Import the combined migration file
   mysql -u username -p database_name < create_modules_tables.sql
   ```

2. **Or run individual migrations:**
   ```bash
   # File Management tables
   mysql -u username -p database_name < create_file_management_tables.sql
   
   # Incident & Safety tables
   mysql -u username -p database_name < create_incident_safety_tables.sql
   ```

3. **Create upload directories:**
   ```bash
   mkdir -p writable/uploads/files/1/1
   mkdir -p writable/uploads/incidents/1/1
   chmod -R 755 writable/uploads
   ```

---

## 4. FILE STRUCTURE

### Controllers
```
app/Controllers/
├── FileManagement.php          - File management operations
└── IncidentSafety.php          - Incident and safety operations
```

### Models
```
app/Models/
├── FileModel.php               - File management
├── FileVersionModel.php        - File versions
├── FileCategoryModel.php       - File categories
├── FileAccessControlModel.php  - Access control
├── FileTagModel.php            - File tags
├── FileCommentModel.php        - File comments
├── FileChangeLogModel.php      - Change tracking
├── IncidentModel.php           - Incidents
├── IncidentTypeModel.php       - Incident types
├── IncidentSeverityModel.php   - Severity levels
├── IncidentPhotoModel.php      - Incident photos
├── IncidentActionStepModel.php - Action steps
├── SafetyAuditModel.php        - Safety audits
├── SafetyAnalyticsModel.php    - Analytics
└── SafetyReportModel.php       - Safety reports
```

### Views
```
app/Views/
├── filemanagement/
│   ├── index.php               - File list
│   ├── view.php                - File details
│   ├── by_category.php         - Files by category
│   └── ...
└── incidentsafety/
    ├── dashboard.php           - Safety dashboard
    ├── incidents/
    │   ├── list.php            - Incident list
    │   ├── create.php          - Create incident
    │   ├── view.php            - Incident details
    │   └── ...
    ├── audits/
    │   ├── list.php            - Audit list
    │   └── ...
    └── reports/
        └── ...
```

---

## 5. INTEGRATION NOTES

### Permissions & Access Control

File access is controlled through:
1. File owner (uploader) has full access
2. Granular access control per user
3. Role-based access (if using role system)
4. Time-limited access with expiration

### Security Features

- File path verification
- User authorization checks
- IP address logging for audit trail
- Secure file download with verification
- Access revocation support

### File Upload Handling

- Files stored in `writable/uploads/files/{company_id}/{project_id}/`
- Incident photos in `writable/uploads/incidents/{company_id}/{incident_id}/`
- Original filename preserved
- File type and size validation

### Incident Code Format

- Format: `INC-YYYYMM-##### (e.g., INC-202601-00001)`
- Unique per company
- Auto-generated on creation

### Audit Code Format

- Format: `AUDIT-YYYYMM-##### (e.g., AUDIT-202601-00001)`
- Unique per company
- Auto-generated on creation

### Report Code Format

- Format: `SAFREP-YYYY-##### (e.g., SAFREP-2026-00001)`
- Unique per company
- Auto-generated on creation

---

## 6. NAVIGATION MENU ITEMS

Add these to your main menu:

```html
<!-- File Management -->
<a href="/file-management" class="nav-link">
    <i class="fas fa-folder-open"></i> File Management
</a>

<!-- Incident & Safety -->
<a href="/incident-safety/dashboard" class="nav-link">
    <i class="fas fa-exclamation-triangle"></i> Incident & Safety
</a>
```

---

## 7. CONFIGURATION

### File Upload Limits

Edit `app/Config/App.php`:
```php
public $maxUploadSize = 10485760; // 10MB default
```

### File Retention

File expiration is optional and configurable per file.

### Database Indexes

All critical query fields are indexed for performance:
- Project filtering
- Company filtering
- Date range searches
- Status filtering
- Type filtering

---

## 8. TROUBLESHOOTING

### File Upload Issues

1. Check directory permissions: `chmod 755 writable/uploads`
2. Verify file size limits in PHP: `php.ini` upload_max_filesize
3. Check database storage limits

### Access Control Issues

1. Verify user ID in session
2. Check access_controls table for valid entries
3. Ensure expiration dates are in future

### Incident Creation Issues

1. Verify project exists
2. Check incident_type_id and severity_id exist
3. Ensure all required fields are populated

---

## 9. FUTURE ENHANCEMENTS

Potential features for future development:

- **File Management:**
  - Cloud storage integration (S3, Google Drive)
  - Document preview generation
  - Full-text search
  - Automatic archiving
  - Batch operations

- **Incident & Safety:**
  - Automated incident escalation
  - Email notifications
  - Dashboard widgets
  - Advanced analytics/charts
  - Compliance reporting
  - Integration with HR system
  - Safety training tracking
  - KPI monitoring

---

## 10. SUPPORT & MAINTENANCE

### Regular Maintenance Tasks

1. **Monthly:** Review open incidents and overdue action steps
2. **Quarterly:** Generate safety reports and trend analysis
3. **Annually:** Archive completed incidents and audit old files
4. **As needed:** Backup file content and database

### Common Queries

- Count incidents by severity: See `IncidentModel::getCriticalIncidents()`
- Calculate safety trends: See `SafetyAnalyticsModel::calculateTrend()`
- Get compliance percentage: See `SafetyAuditModel::getHighConformanceRate()`

---

## Contact & Support

For issues, questions, or feature requests related to these modules, please contact the development team.

---

**Last Updated:** February 3, 2026
**Version:** 1.0.0
