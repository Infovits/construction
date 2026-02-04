@echo off
REM ============================================================
REM File Management & Incident/Safety Modules Installation
REM Windows Batch Script
REM ============================================================

setlocal enabledelayedexpansion

echo.
echo ==========================================
echo Installation Script for New Modules
echo ==========================================
echo.

REM Check if database name is provided
if "%1"=="" (
    echo Usage: install_modules.bat ^<database_name^> [mysql_user] [mysql_password]
    echo Example: install_modules.bat construction root password
    pause
    exit /b 1
)

set DB_NAME=%1
set DB_USER=%2
if "%DB_USER%"=="" set DB_USER=root
set DB_PASS=%3

echo Configuration:
echo Database: %DB_NAME%
echo User: %DB_USER%
echo.

REM Step 1: Import Database
echo Step 1: Importing database tables...
if "%DB_PASS%"=="" (
    mysql -u %DB_USER% %DB_NAME% < create_modules_tables.sql
) else (
    mysql -u %DB_USER% -p%DB_PASS% %DB_NAME% < create_modules_tables.sql
)

if %errorlevel% equ 0 (
    echo [OK] Database tables imported successfully
) else (
    echo [ERROR] Failed to import database tables
    pause
    exit /b 1
)

echo.

REM Step 2: Create Upload Directories
echo Step 2: Creating upload directories...

if not exist "writable\uploads\files\1" (
    mkdir "writable\uploads\files\1"
    echo [OK] Created directory: writable\uploads\files\1
) else (
    echo [INFO] Directory exists: writable\uploads\files\1
)

if not exist "writable\uploads\incidents\1" (
    mkdir "writable\uploads\incidents\1"
    echo [OK] Created directory: writable\uploads\incidents\1
) else (
    echo [INFO] Directory exists: writable\uploads\incidents\1
)

echo.

REM Step 3: Verify Files
echo Step 3: Verifying source files...

set FILES=^
    app\Controllers\FileManagement.php ^
    app\Controllers\IncidentSafety.php ^
    app\Models\FileModel.php ^
    app\Models\IncidentModel.php ^
    app\Views\filemanagement\index.php ^
    app\Views\incidentsafety\dashboard.php

for %%F in (%FILES%) do (
    if exist "%%F" (
        echo [OK] Found: %%F
    ) else (
        echo [ERROR] Missing: %%F
    )
)

echo.

REM Step 4: Check Routes
echo Step 4: Checking Routes configuration...

findstr /M "file-management" app\Config\Routes.php >nul
if %errorlevel% equ 0 (
    echo [OK] File Management routes configured
) else (
    echo [ERROR] File Management routes not found
)

findstr /M "incident-safety" app\Config\Routes.php >nul
if %errorlevel% equ 0 (
    echo [OK] Incident and Safety routes configured
) else (
    echo [ERROR] Incident and Safety routes not found
)

echo.

REM Step 5: Summary
echo ==========================================
echo Installation Complete!
echo ==========================================
echo.
echo Next Steps:
echo 1. Clear CodeIgniter cache: spark cache:clear
echo 2. Access File Management: http://localhost/file-management
echo 3. Access Safety Dashboard: http://localhost/incident-safety/dashboard
echo.
echo Documentation:
echo - Full Docs: MODULES_DOCUMENTATION.md
echo - Quick Start: MODULES_IMPLEMENTATION_SUMMARY.md
echo.

pause
