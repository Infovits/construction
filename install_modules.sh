#!/bin/bash

# ============================================================
# File Management & Incident/Safety Modules Installation
# ============================================================

echo "=========================================="
echo "Installation Script for New Modules"
echo "=========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if database credentials are provided
if [ -z "$1" ]; then
    echo -e "${RED}Usage: $0 <database_name> [mysql_user] [mysql_password]${NC}"
    echo "Example: $0 construction root password"
    exit 1
fi

DB_NAME=$1
DB_USER=${2:-root}
DB_PASS=${3:-}

echo -e "${YELLOW}Configuration:${NC}"
echo "Database: $DB_NAME"
echo "User: $DB_USER"
echo ""

# Step 1: Import Database
echo -e "${YELLOW}Step 1: Importing database tables...${NC}"

if [ -z "$DB_PASS" ]; then
    mysql -u "$DB_USER" "$DB_NAME" < create_modules_tables.sql
else
    mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < create_modules_tables.sql
fi

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Database tables imported successfully${NC}"
else
    echo -e "${RED}✗ Failed to import database tables${NC}"
    exit 1
fi

echo ""

# Step 2: Create Upload Directories
echo -e "${YELLOW}Step 2: Creating upload directories...${NC}"

DIRS=(
    "writable/uploads/files/1"
    "writable/uploads/incidents/1"
)

for dir in "${DIRS[@]}"; do
    if [ ! -d "$dir" ]; then
        mkdir -p "$dir"
        echo -e "${GREEN}✓ Created directory: $dir${NC}"
    else
        echo -e "${YELLOW}→ Directory exists: $dir${NC}"
    fi
done

# Set permissions
chmod -R 755 writable/uploads
echo -e "${GREEN}✓ Set permissions on upload directories${NC}"

echo ""

# Step 3: Verify Files
echo -e "${YELLOW}Step 3: Verifying source files...${NC}"

FILES=(
    "app/Controllers/FileManagement.php"
    "app/Controllers/IncidentSafety.php"
    "app/Models/FileModel.php"
    "app/Models/IncidentModel.php"
    "app/Views/filemanagement/index.php"
    "app/Views/incidentsafety/dashboard.php"
)

for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓ Found: $file${NC}"
    else
        echo -e "${RED}✗ Missing: $file${NC}"
    fi
done

echo ""

# Step 4: Check Routes
echo -e "${YELLOW}Step 4: Checking Routes configuration...${NC}"

if grep -q "file-management" app/Config/Routes.php; then
    echo -e "${GREEN}✓ File Management routes configured${NC}"
else
    echo -e "${RED}✗ File Management routes not found${NC}"
fi

if grep -q "incident-safety" app/Config/Routes.php; then
    echo -e "${GREEN}✓ Incident & Safety routes configured${NC}"
else
    echo -e "${RED}✗ Incident & Safety routes not found${NC}"
fi

echo ""

# Step 5: Summary
echo "=========================================="
echo -e "${GREEN}Installation Complete!${NC}"
echo "=========================================="
echo ""
echo "Next Steps:"
echo "1. Clear CodeIgniter cache: spark cache:clear"
echo "2. Access File Management: http://localhost/file-management"
echo "3. Access Safety Dashboard: http://localhost/incident-safety/dashboard"
echo ""
echo "Documentation:"
echo "- Full Docs: MODULES_DOCUMENTATION.md"
echo "- Quick Start: MODULES_IMPLEMENTATION_SUMMARY.md"
echo ""
