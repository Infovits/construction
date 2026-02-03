 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? get_company_name() ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <?= $this->renderSection('head') ?>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#8b5cf6',
                    }
                }
            }
        }
    </script>
    <style>
        .sidebar-collapsed {
            width: 4rem;
        }
        .sidebar-expanded {
            width: 16rem;
        }
        .sidebar-text {
            opacity: 1;
            transition: opacity 0.3s ease;
        }
        .sidebar-collapsed .sidebar-text {
            opacity: 0;
            pointer-events: none;
        }
        .sidebar-transition {
            transition: width 0.3s ease;
        }
        .sidebar-mobile {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        .sidebar-mobile.open {
            transform: translateX(0);
        }
        @media (min-width: 768px) {
            .sidebar-mobile {
                transform: translateX(0);
            }
        }
        .tooltip {
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.2s, visibility 0.2s;
        }
        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        .submenu.open {
            max-height: 500px;
            overflow-y: auto;
        }
        .menu-chevron {
            transition: transform 0.3s ease;
        }
        .menu-chevron.rotated {
            transform: rotate(180deg);
        }
        .nav-item.active {
            font-weight: 600;
            color: #4f46e5;
            background-color: #eef2ff;
        }
        .submenu a.active {
            font-weight: 600;
            color: #4f46e5;
            background-color: #f8fafc;
            border-left: 3px solid #4f46e5;
        }
        .sidebar-collapsed .nav-item:hover .tooltip {
            visibility: visible;
            opacity: 1;
        }
        .sidebar-collapsed .submenu {
            display: none;
        }
        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 30;
        }
        /* Custom Scrollbar for Sidebar */
        nav::-webkit-scrollbar {
            width: 4px;
        }
        nav::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        nav::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 2px;
        }
        nav::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .submenu::-webkit-scrollbar {
            width: 3px;
        }
        .submenu::-webkit-scrollbar-track {
            background: #f8fafc;
        }
        .submenu::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 2px;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Mobile Overlay -->
        <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>
        
        <!-- Sidebar -->
        <div id="sidebar" class="fixed md:relative z-50 bg-white shadow-lg sidebar-transition sidebar-expanded sidebar-mobile h-full flex flex-col">
            <div class="p-4 md:p-6">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-pink-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        <?php 
                        helper('settings');
                        $logo = get_company_logo();
                        if (strpos($logo, 'logo-placeholder') === false): ?>
                            <img src="<?= $logo ?>" alt="<?= get_company_name() ?>" class="w-full h-full object-contain rounded-lg">
                        <?php else: ?>
                            <i data-lucide="building-2" class="w-5 h-5 text-white"></i>
                        <?php endif; ?>
                    </div>
                    <div class="sidebar-text overflow-hidden">
                        <h1 class="text-lg md:text-xl font-bold text-gray-800 whitespace-nowrap"><?= get_company_name() ?></h1>
                        <p class="text-xs md:text-sm text-gray-500 whitespace-nowrap">Management System</p>
                    </div>
                </div>
            </div>
            
            <nav class="mt-6 md:mt-8 flex-1 overflow-y-auto pb-20">
                <div class="px-4 md:px-6 py-3 bg-indigo-50 border-r-4 border-indigo-500">
                    <a href="#" class="flex items-center space-x-3 text-indigo-600 nav-item relative" onclick="toggleSubmenu(event, 'dashboard-submenu')">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 flex-shrink-0"></i>
                        <span class="font-medium sidebar-text overflow-hidden whitespace-nowrap">Dashboard</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 ml-auto sidebar-text menu-chevron" id="dashboard-chevron"></i>
                        <div class="tooltip absolute left-16 bg-gray-800 text-white px-2 py-1 rounded text-sm whitespace-nowrap">
                            Dashboard
                        </div>
                    <div class="submenu" id="dashboard-submenu">
                        <div class="sidebar-text ml-8 mt-2 space-y-1">
                            <a href="<?= base_url('admin/dashboard') ?>" class="block py-2 text-sm text-indigo-500 hover:text-indigo-700">Main Dashboard</a>
                            <a href="<?= base_url('admin/analytics') ?>" class="block py-2 text-sm text-indigo-500 hover:text-indigo-700">Analytics</a>
                            <a href="<?= base_url('admin/reports') ?>" class="block py-2 text-sm text-indigo-500 hover:text-indigo-700">Reports</a>
                            <a href="<?= base_url('admin/overview') ?>" class="block py-2 text-sm text-indigo-500 hover:text-indigo-700">Overview</a>
                        </div>
                    </div>
                </div>
                
                <div class="px-4 md:px-6 py-3 hover:bg-gray-50 transition-colors">
                    <a href="#" class="flex items-center space-x-3 text-gray-600 nav-item relative" onclick="toggleSubmenu(event, 'project-submenu')">
                        <i data-lucide="folder-open" class="w-5 h-5 flex-shrink-0"></i>
                        <span class="sidebar-text overflow-hidden whitespace-nowrap">Project Management</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 ml-auto sidebar-text menu-chevron" id="project-chevron"></i>
                        <div class="tooltip absolute left-16 bg-gray-800 text-white px-2 py-1 rounded text-sm whitespace-nowrap">
                            Project Management
                        </div>
                    <div class="submenu" id="project-submenu">
                        <div class="sidebar-text ml-8 mt-2 space-y-1">
                            <a href="<?= base_url('admin/projects') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">All Projects</a>
                            <a href="<?= base_url('admin/projects/create') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">New Project</a>
                            <a href="<?= base_url('admin/project-categories') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Categories</a>
                        </div>
                    </div>
                </div>
                
                <div class="px-4 md:px-6 py-3 hover:bg-gray-50 transition-colors">
                    <a href="#" class="flex items-center space-x-3 text-gray-600 nav-item relative" onclick="toggleSubmenu(event, 'tasks-submenu')">
                        <i data-lucide="check-square" class="w-5 h-5 flex-shrink-0"></i>
                        <span class="sidebar-text overflow-hidden whitespace-nowrap">Tasks</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 ml-auto sidebar-text menu-chevron" id="tasks-chevron"></i>
                        <div class="tooltip absolute left-16 bg-gray-800 text-white px-2 py-1 rounded text-sm whitespace-nowrap">
                            Tasks
                        </div>
                    </a>
                    <div class="submenu" id="tasks-submenu">
                        <div class="sidebar-text ml-8 mt-2 space-y-1">
                            <a href="<?= base_url('admin/tasks') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">All Tasks</a>
                            <a href="<?= base_url('admin/tasks/create') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">New Task</a>
                            <a href="<?= base_url('admin/tasks/calendar') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Calendar View</a>
                            <a href="<?= base_url('admin/tasks?status=pending') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Pending Tasks</a>
                            <a href="<?= base_url('admin/tasks?status=in_progress') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">In Progress</a>
                            <a href="<?= base_url('admin/tasks/report') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Reports</a>
                        </div>
                    </div>
                </div>
                
                <div class="px-4 md:px-6 py-3 hover:bg-gray-50 transition-colors">
                    <a href="#" class="flex items-center space-x-3 text-gray-600 nav-item relative" onclick="toggleSubmenu(event, 'milestones-submenu')">
                        <i data-lucide="flag" class="w-5 h-5 flex-shrink-0"></i>
                        <span class="sidebar-text overflow-hidden whitespace-nowrap">Milestones</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 ml-auto sidebar-text menu-chevron" id="milestones-chevron"></i>
                        <div class="tooltip absolute left-16 bg-gray-800 text-white px-2 py-1 rounded text-sm whitespace-nowrap">
                            Milestones
                        </div>
                    </a>
                    <div class="submenu" id="milestones-submenu">
                        <div class="sidebar-text ml-8 mt-2 space-y-1">
                            <a href="<?= base_url('admin/milestones') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">All Milestones</a>
                            <a href="<?= base_url('admin/milestones/create') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">New Milestone</a>
                            <a href="<?= base_url('admin/milestones?status=upcoming') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Upcoming</a>
                            <a href="<?= base_url('admin/milestones?status=completed') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Completed</a>
                        </div>
                    </div>
                </div>
                
                <div class="px-4 md:px-6 py-3 hover:bg-gray-50 transition-colors">
                    <a href="#" class="flex items-center space-x-3 text-gray-600 nav-item relative" onclick="toggleSubmenu(event, 'clients-submenu')">
                        <i data-lucide="users" class="w-5 h-5 flex-shrink-0"></i>
                        <span class="sidebar-text overflow-hidden whitespace-nowrap">Clients</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 ml-auto sidebar-text menu-chevron" id="clients-chevron"></i>
                        <div class="tooltip absolute left-16 bg-gray-800 text-white px-2 py-1 rounded text-sm whitespace-nowrap">
                            Clients
                        </div>
                    </a>
                    <div class="submenu" id="clients-submenu">
                        <div class="sidebar-text ml-8 mt-2 space-y-1">
                            <a href="<?= base_url('admin/clients') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">All Clients</a>
                            <a href="<?= base_url('admin/clients/create') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">New Client</a>
                        </div>
                    </div>
                </div>
                
                <div class="px-4 md:px-6 py-3 hover:bg-gray-50 transition-colors">
                    <a href="#" class="flex items-center space-x-3 text-gray-600 nav-item relative" onclick="toggleSubmenu(event, 'message-submenu')">
                        <i data-lucide="message-circle" class="w-5 h-5 flex-shrink-0"></i>
                        <span class="sidebar-text overflow-hidden whitespace-nowrap">Message</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 ml-auto sidebar-text menu-chevron" id="message-chevron"></i>
                        <div class="tooltip absolute left-16 bg-gray-800 text-white px-2 py-1 rounded text-sm whitespace-nowrap">
                            Message
                        </div>
                    </a>
                    <div class="submenu" id="message-submenu">
                        <div class="sidebar-text ml-8 mt-2 space-y-1">
                            <a href="<?= base_url('admin/messages') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Inbox</a>
                            <a href="<?= base_url('admin/messages/new') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">New Message</a>
                            <a href="<?= base_url('admin/messages/sent') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Sent</a>
                            <a href="<?= base_url('admin/messages/drafts') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Drafts</a>
                        </div>
                    </div>
                </div>
                
                <div class="px-4 md:px-6 py-3 hover:bg-gray-50 transition-colors">
                    <a href="#" class="flex items-center space-x-3 text-gray-600 nav-item relative" onclick="toggleSubmenu(event, 'inventory-submenu')">
                        <i data-lucide="package" class="w-5 h-5 flex-shrink-0"></i>
                        <span class="sidebar-text overflow-hidden whitespace-nowrap">Inventory</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 ml-auto sidebar-text menu-chevron" id="inventory-chevron"></i>
                        <div class="tooltip absolute left-16 bg-gray-800 text-white px-2 py-1 rounded text-sm whitespace-nowrap">
                            Inventory
                        </div>
                    </a>
                    <div class="submenu" id="inventory-submenu">
                        <div class="sidebar-text ml-8 mt-2 space-y-1">
                            <a href="<?= base_url('admin/suppliers') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Suppliers</a>
                            <a href="<?= base_url('admin/material-categories') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Categories</a>
                            <a href="<?= base_url('admin/materials') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Materials</a>
                            <a href="<?= base_url('admin/warehouses') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Warehouses</a>
                            <a href="<?= base_url('admin/materials/barcode-scanner') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Barcode Scanner</a>
                            <a href="<?= base_url('admin/materials/low-stock-notifications') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Low Stock</a>
                            <a href="<?= base_url('admin/materials/report') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Reports</a>
                        </div>
                    </div>
                </div>



                <!-- Procurement Management Section -->
                <div class="px-4 md:px-6 py-3 hover:bg-gray-50 transition-colors">
                    <a href="#" class="flex items-center space-x-3 text-gray-600 nav-item relative" onclick="toggleSubmenu(event, 'procurement-submenu')">
                        <i data-lucide="shopping-cart" class="w-5 h-5 flex-shrink-0"></i>
                        <span class="sidebar-text overflow-hidden whitespace-nowrap">Procurement</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 ml-auto sidebar-text menu-chevron" id="procurement-chevron"></i>
                        <div class="tooltip absolute left-16 bg-gray-800 text-white px-2 py-1 rounded text-sm whitespace-nowrap">
                            Procurement
                        </div>
                    </a>
                    <div class="submenu" id="procurement-submenu">
                        <div class="sidebar-text ml-8 mt-2 space-y-1">
                            <a href="<?= base_url('admin/procurement/material-requests') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Material Requests</a>
                            <a href="<?= base_url('admin/procurement/purchase-orders') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Purchase Orders</a>
                            <a href="<?= base_url('admin/procurement/goods-receipt') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Goods Receipt</a>
                            <a href="<?= base_url('admin/procurement/quality-inspections') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Quality Inspections</a>
                            <a href="<?= base_url('admin/procurement/reports') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Procurement Reports</a>
                        </div>
                    </div>
                </div>

                <!-- Accounting Module -->
                <div class="px-4 md:px-6 py-3 hover:bg-gray-50 transition-colors">
                    <a href="#" class="flex items-center space-x-3 text-gray-600 nav-item relative" onclick="toggleSubmenu(event, 'accounting-submenu')">
                        <i data-lucide="calculator" class="w-5 h-5 flex-shrink-0"></i>
                        <span class="sidebar-text overflow-hidden whitespace-nowrap">Accounting</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 ml-auto sidebar-text menu-chevron" id="accounting-chevron"></i>
                        <div class="tooltip absolute left-16 bg-gray-800 text-white px-2 py-1 rounded text-sm whitespace-nowrap">
                            Accounting
                        </div>
                    </a>
                    <div class="submenu" id="accounting-submenu">
                        <div class="sidebar-text ml-8 mt-2 space-y-1">
                            <!-- Setup & Configuration -->
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide py-1 border-b border-gray-200 mb-2">Setup</div>
                            <a href="<?= base_url('admin/accounting/chart-of-accounts') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-2 rounded">Chart of Accounts</a>

                            <!-- Core Transactions -->
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide py-1 border-b border-gray-200 mb-2 mt-4">Transactions</div>
                            <a href="<?= base_url('admin/accounting/journal-entries') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-2 rounded">Journal Entries</a>
                            <a href="<?= base_url('admin/accounting/general-ledger') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-2 rounded">General Ledger</a>
                            
                            <!-- Job Costing -->
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide py-1 border-b border-gray-200 mb-2 mt-4">Job Costing</div>
                            <a href="<?= base_url('admin/accounting/job-cost-tracking') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-2 rounded">Job Cost Tracking</a>
                            <a href="<?= base_url('admin/accounting/job-budgets') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-2 rounded">Job Budgets</a>
                            <a href="<?= base_url('admin/accounting/budget-categories') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-2 rounded">Budget Categories</a>
                            <a href="<?= base_url('admin/accounting/cost-codes') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-2 rounded">Cost Codes</a>

                            <!-- Accounts Payable -->
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide py-1 border-b border-gray-200 mb-2 mt-4">Accounts Payable</div>
                            <a href="<?= base_url('admin/accounting/vendor-bills') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-2 rounded">Vendor Bills</a>
                            <a href="<?= base_url('admin/accounting/bill-payments') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-2 rounded">Bill Payments</a>
                            <a href="<?= base_url('admin/accounting/vendors') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-2 rounded">Vendor Management</a>
                            
                            <!-- Accounts Receivable -->
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide py-1 border-b border-gray-200 mb-2 mt-4">Accounts Receivable</div>
                            <a href="<?= base_url('admin/accounting/customer-invoices') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-2 rounded">Customer Invoices</a>
                            <a href="<?= base_url('admin/accounting/invoice-payments') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-2 rounded">Invoice Payments</a>
                            <a href="<?= base_url('admin/accounting/retainage') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-2 rounded">Retainage Management</a>
                            <a href="<?= base_url('admin/accounting/progress-billing') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-2 rounded">Progress Billing</a>
                            
                            <!-- Banking & Cash -->
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide py-1 border-b border-gray-200 mb-2 mt-4">Banking</div>
                            <a href="<?= base_url('admin/accounting/bank-accounts') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-2 rounded">Bank Accounts</a>
                            <a href="<?= base_url('admin/accounting/bank-reconciliation') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-2 rounded">Bank Reconciliation</a>
                            <a href="<?= base_url('admin/accounting/cash-flow') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-2 rounded">Cash Flow</a>
                            
                            <!-- Financial Reports -->
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide py-1 border-b border-gray-200 mb-2 mt-4">Reports</div>
                            <a href="<?= base_url('admin/accounting/reports') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-2 rounded">Financial Reports</a>
                            <a href="<?= base_url('admin/accounting/trial-balance') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-2 rounded">Trial Balance</a>
                            <a href="<?= base_url('admin/accounting/profit-loss') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-2 rounded">Profit & Loss</a>
                            <a href="<?= base_url('admin/accounting/balance-sheet') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-2 rounded">Balance Sheet</a>
                            <a href="<?= base_url('admin/accounting/cash-flow-statement') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-2 rounded">Cash Flow Statement</a>
                            <a href="<?= base_url('admin/accounting/job-cost-reports') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-2 rounded">Job Cost Reports</a>
                            <a href="<?= base_url('admin/accounting/aging-reports') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 px-2 rounded">Aging Reports</a>
                        </div>
                    </div>
                </div>




                <!-- HR & Administration Section -->
                <div class="px-4 md:px-6 py-3 hover:bg-gray-50 transition-colors">
                    <a href="#" class="flex items-center space-x-3 text-gray-600 nav-item relative" onclick="toggleSubmenu(event, 'hr-submenu')">
                        <i data-lucide="user-check" class="w-5 h-5 flex-shrink-0"></i>
                        <span class="sidebar-text overflow-hidden whitespace-nowrap">HR & Admin</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 ml-auto sidebar-text menu-chevron" id="hr-chevron"></i>
                        <div class="tooltip absolute left-16 bg-gray-800 text-white px-2 py-1 rounded text-sm whitespace-nowrap">
                            HR & Administration
                        </div>
                    </a>
                    <div class="submenu" id="hr-submenu">
                        <div class="sidebar-text ml-8 mt-2 space-y-1">
                            <a href="<?= base_url('admin/users') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Users</a>
                            <a href="<?= base_url('admin/roles') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Roles & Permissions</a>
                            <a href="<?= base_url('admin/departments') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Departments</a>
                            <a href="<?= base_url('admin/positions') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Job Positions</a>
                        </div>
                    </div>
                </div>
                
                <div class="px-4 md:px-6 py-3 hover:bg-gray-50 transition-colors">
                    <a href="#" class="flex items-center space-x-3 text-gray-600 nav-item relative" onclick="toggleSubmenu(event, 'settings-submenu')">
                        <i data-lucide="settings" class="w-5 h-5 flex-shrink-0"></i>
                        <span class="sidebar-text overflow-hidden whitespace-nowrap">Settings</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 ml-auto sidebar-text menu-chevron" id="settings-chevron"></i>
                        <div class="tooltip absolute left-16 bg-gray-800 text-white px-2 py-1 rounded text-sm whitespace-nowrap">
                            Settings
                        </div>
                    </a>
                    <div class="submenu" id="settings-submenu">
                        <div class="sidebar-text ml-8 mt-2 space-y-1">
                            <a href="<?= base_url('admin/settings/general') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">General</a>
                            <a href="<?= base_url('admin/settings/security') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Security</a>
                            <a href="<?= base_url('admin/settings/preferences') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Preferences</a>
                            <a href="<?= base_url('admin/settings/integrations') ?>" class="block py-2 text-sm text-gray-500 hover:text-gray-700">Integrations</a>
                        </div>
                    </div>
                </div>
                
                <div class="px-4 md:px-6 py-3 hover:bg-gray-50 transition-colors">
                    <a href="#" class="flex items-center space-x-3 text-gray-600 nav-item relative">
                        <i data-lucide="credit-card" class="w-5 h-5 flex-shrink-0"></i>
                        <span class="sidebar-text overflow-hidden whitespace-nowrap">Plans</span>
                        <div class="tooltip absolute left-16 bg-gray-800 text-white px-2 py-1 rounded text-sm whitespace-nowrap">
                            Plans
                        </div>
                    </a>
                </div>
            </nav>
              <div class="absolute bottom-0 w-full p-4 md:p-6">
                <a href="<?= base_url('auth/logout') ?>" 
                   class="flex items-center space-x-3 text-gray-600 hover:text-gray-800 nav-item relative transition-colors duration-200"
                   onclick="return confirm('Are you sure you want to logout?')">
                    <i data-lucide="log-out" class="w-5 h-5 flex-shrink-0"></i>
                    <span class="sidebar-text overflow-hidden whitespace-nowrap">Log Out</span>
                    <div class="tooltip absolute left-16 bg-gray-800 text-white px-2 py-1 rounded text-sm whitespace-nowrap">
                        Log Out
                    </div>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div id="mainContent" class="flex-1 overflow-auto">
            <!-- Sticky Header -->
            <div class="sticky-header bg-white border-b border-gray-200 px-4 md:px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Mobile Menu Button -->
                        <button id="mobileMenuBtn" class="p-2 hover:bg-gray-100 rounded-lg md:hidden">
                            <i data-lucide="menu" class="w-5 h-5 text-gray-600"></i>
                        </button>
                        
                        <!-- Desktop Sidebar Toggle -->
                        <button id="sidebarToggle" class="p-2 hover:bg-gray-100 rounded-lg hidden md:block">
                            <i data-lucide="menu" class="w-5 h-5 text-gray-600"></i>
                        </button>
                        
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800"><?= $pageTitle ?? 'Dashboard' ?></h2>
                    </div>
                    
                    <div class="flex items-center space-x-2 md:space-x-4">
                        <div class="relative hidden md:block">
                            <input type="text" placeholder="Search" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 w-48 lg:w-64">
                            <i data-lucide="search" class="w-4 h-4 absolute left-3 top-3 text-gray-400"></i>
                        </div>
                        
                        <!-- Mobile Search Button -->
                        <button class="p-2 hover:bg-gray-100 rounded-lg md:hidden">
                            <i data-lucide="search" class="w-5 h-5 text-gray-600"></i>
                        </button>
                        
                        <div class="flex items-center space-x-2 md:space-x-4">
                            <!-- Notification Bell -->
                            <div class="relative">
                                <button id="notificationBtn" class="p-2 hover:bg-gray-100 rounded-lg relative" onclick="toggleNotifications()">
                                    <i data-lucide="bell" class="w-5 h-5 text-gray-600"></i>
                                    <span id="notificationBadge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
                                </button>
                                
                                <!-- Notification Dropdown -->
                                <div id="notificationDropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-50 max-h-96 overflow-y-auto">
                                    <div class="p-4 border-b border-gray-200">
                                        <h3 class="font-semibold text-gray-800">Notifications</h3>
                                    </div>
                                    <div id="notificationList" class="divide-y divide-gray-200">
                                        <!-- Notifications will be loaded here -->
                                    </div>
                                    <div class="p-3 border-t border-gray-200">
                                        <button onclick="viewAllNotifications()" class="w-full text-center text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                            View All Notifications
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="hidden md:flex items-center space-x-2">
                                <span class="text-sm text-gray-600">ID</span>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                            </div>
                        </div>
                          <div class="relative">
                            <div class="flex items-center space-x-2 md:space-x-3 cursor-pointer" onclick="toggleUserDropdown()">
                                <div class="w-8 h-8 bg-gray-800 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-medium"><?= strtoupper(substr(session('first_name'), 0, 1)) ?></span>
                                </div>
                                <div class="text-sm hidden md:block">
                                    <div class="font-medium text-gray-800"><?= session('full_name') ?? 'User' ?></div>
                                    <div class="text-gray-500 text-xs"><?= session('email') ?? 'user@email.com' ?></div>
                                </div>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 hidden md:block"></i>
                            </div>
                            
                            <!-- User Dropdown Menu -->
                            <div id="userDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-50">
                                <div class="py-1">
                                    <a href="<?= base_url('admin/users/profile') ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i data-lucide="user" class="w-4 h-4 mr-2"></i>
                                        Profile
                                    </a>
                                    <a href="<?= base_url('admin/settings') ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i data-lucide="settings" class="w-4 h-4 mr-2"></i>
                                        Settings
                                    </a>
                                    <hr class="my-1">
                                    <a href="<?= base_url('auth/logout') ?>" 
                                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-red-600"
                                       onclick="return confirm('Are you sure you want to logout?')">
                                        <i data-lucide="log-out" class="w-4 h-4 mr-2"></i>
                                        Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="p-4 md:p-6">
                <!-- Flash Messages -->
                <?php if (session('success')): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg relative" role="alert">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                            </div>
                            <div>
                                <p class="font-medium"><?= session('success') ?></p>
                            </div>
                            <button type="button" class="ml-auto -mx-1.5 -my-1.5 text-green-500 p-1.5 hover:text-green-800 rounded-lg focus:ring-2 focus:ring-green-400" onclick="this.parentElement.parentElement.remove()">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (session('error')): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg relative" role="alert">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i data-lucide="alert-circle" class="w-5 h-5 mr-2"></i>
                            </div>
                            <div>
                                <p class="font-medium"><?= session('error') ?></p>
                            </div>
                            <button type="button" class="ml-auto -mx-1.5 -my-1.5 text-red-500 p-1.5 hover:text-red-800 rounded-lg focus:ring-2 focus:ring-red-400" onclick="this.parentElement.parentElement.remove()">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (session('warning')): ?>
                    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded-lg relative" role="alert">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i data-lucide="alert-triangle" class="w-5 h-5 mr-2"></i>
                            </div>
                            <div>
                                <p class="font-medium"><?= session('warning') ?></p>
                            </div>
                            <button type="button" class="ml-auto -mx-1.5 -my-1.5 text-yellow-500 p-1.5 hover:text-yellow-800 rounded-lg focus:ring-2 focus:ring-yellow-400" onclick="this.parentElement.parentElement.remove()">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Debug Section for Validation Errors -->
                <?php $validation = \Config\Services::validation(); ?>
                <?php if ($validation->getErrors() && ENVIRONMENT === 'development'): ?>
                    <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i data-lucide="info" class="w-5 h-5 mr-2"></i>
                            </div>
                            <div>
                                <p class="font-medium">Validation Debug Information:</p>
                                <pre class="mt-2 text-sm bg-blue-50 p-2 rounded overflow-auto max-h-64"><?= print_r($validation->getErrors(), true) ?></pre>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Validation Errors Summary (if any) -->
                <?php if ($validation->getErrors()): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i data-lucide="alert-octagon" class="w-5 h-5 mr-2"></i>
                            </div>
                            <div>
                                <p class="font-medium">Please fix the following errors:</p>
                                <ul class="mt-1 ml-4 list-disc list-inside text-sm">
                                    <?php foreach($validation->getErrors() as $field => $error): ?>
                                        <li><strong><?= ucfirst(str_replace('_', ' ', $field)) ?>:</strong> <?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

        <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        const sidebar = document.getElementById('sidebar');

        // Set active states and open relevant dropdowns on page load
        function setActiveStates() {
            const currentPath = window.location.pathname;
            console.log('Current path:', currentPath);

            // Define menu sections and their URL patterns
            const menuSections = {
                'dashboard-submenu': /^\/admin\/dashboard/,
                'project-submenu': /^\/admin\/projects/,
                'tasks-submenu': /^\/admin\/tasks/,
                'milestones-submenu': /^\/admin\/milestones/,
                'clients-submenu': /^\/admin\/clients/,
                'message-submenu': /^\/admin\/messages/,
                'inventory-submenu': /^\/admin\/materials|^\/admin\/material-categories|^\/admin\/warehouses|^\/admin\/suppliers/,
                'procurement-submenu': /^\/admin\/procurement/,
                'accounting-submenu': /^\/admin\/accounting/,
                'notification-submenu': /^\/admin\/notification/,
                'hr-submenu': /^\/admin\/users|^\/admin\/roles|^\/admin\/departments|^\/admin\/positions/,
                'settings-submenu': /^\/admin\/settings/
            };

            // Find matching section
            let activeSection = null;
            for (const [section, pattern] of Object.entries(menuSections)) {
                if (pattern.test(currentPath)) {
                    activeSection = section;
                    break;
                }
            }

            // Open the active section's dropdown and highlight active links
            if (activeSection) {
                const submenu = document.getElementById(activeSection);
                const chevron = document.getElementById(activeSection.replace('-submenu', '-chevron'));
                const menuItem = chevron?.parentElement;

                if (submenu && chevron && menuItem) {
                    submenu.classList.add('open');
                    chevron.classList.add('rotated');
                    menuItem.classList.add('active');
                }

                // Find and highlight the active link within the submenu
                const submenuLinks = submenu.querySelectorAll('a');
                submenuLinks.forEach(link => {
                    const linkPath = new URL(link.href, window.location.origin).pathname;
                    if (linkPath === currentPath) {
                        link.classList.add('active');
                    }
                });
            }
        }

        // Submenu toggle functionality
        function toggleSubmenu(event, submenuId) {
            event.preventDefault();
            
            // Don't toggle if sidebar is collapsed
            if (sidebar.classList.contains('sidebar-collapsed')) {
                return;
            }
            
            const submenu = document.getElementById(submenuId);
            const chevron = document.getElementById(submenuId.replace('-submenu', '-chevron'));
            const menuItem = chevron?.parentElement;
            
            // If this submenu is active and already open, don't close it
            if (menuItem && menuItem.classList.contains('active') && submenu.classList.contains('open')) {
                return;
            }
            
            // Close all other submenus
            document.querySelectorAll('.submenu').forEach(menu => {
                if (menu.id !== submenuId && menu.classList.contains('open')) {
                    menu.classList.remove('open');
                    const otherChevron = document.getElementById(menu.id.replace('-submenu', '-chevron'));
                    if (otherChevron) {
                        otherChevron.classList.remove('rotated');
                    }
                }
            });
            
            // Toggle current submenu
            submenu.classList.toggle('open');
            if (chevron) {
                chevron.classList.toggle('rotated');
            }
        }

        // Initialize Lucide icons
        lucide.createIcons();

        // Set active states on page load
        setActiveStates();

        // Load notifications on page load
        loadNotifications();
        setInterval(loadNotifications, 30000);

        // Notification functionality
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            const bellBtn = document.getElementById('notificationBtn');
            
            dropdown.classList.toggle('hidden');
            
            // Load notifications if dropdown is opened
            if (!dropdown.classList.contains('hidden')) {
                loadNotifications();
            }
        }
        
        // Load notifications
        function loadNotifications() {
            const notificationList = document.getElementById('notificationList');
            const notificationBadge = document.getElementById('notificationBadge');

            fetch('<?= base_url('admin/notifications/recent') ?>', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) return;

                const notifications = data.notifications || [];
                const unreadCount = data.unread_count || 0;

                notificationList.innerHTML = '';

                if (notifications.length === 0) {
                    notificationList.innerHTML = '<div class="p-4 text-center text-gray-500">No notifications</div>';
                }

                notifications.forEach(notification => {
                    const isRead = parseInt(notification.is_read, 10) === 1;
                    const notificationItem = document.createElement('div');
                    notificationItem.className = `p-4 ${isRead ? 'bg-gray-50' : 'bg-white'} cursor-pointer hover:bg-indigo-50`;

                    // Build link from related_type and related_id
                    let link = '#';
                    if (notification.related_type === 'conversation' && notification.related_id) {
                        link = '<?= base_url('admin/messages/') ?>' + notification.related_id;
                    }

                    const notificationType = notification.notification_type || 'in_app';
                    const iconColor = notificationType === 'email' ? 'text-blue-500' : 
                                     notificationType === 'push' ? 'text-purple-500' :
                                     notificationType === 'sms' ? 'text-green-500' : 'text-blue-500';

                    notificationItem.innerHTML = `
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center ${iconColor}">
                                <i data-lucide="bell" class="w-4 h-4"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900 text-sm">${notification.title}</h4>
                                <p class="text-gray-600 text-sm mt-1">${notification.message ?? ''}</p>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-xs text-gray-500">${notification.time ?? ''}</span>
                                    ${!isRead ? '<span class="w-2 h-2 bg-indigo-500 rounded-full"></span>' : ''}
                                </div>
                            </div>
                        </div>
                    `;

                    notificationItem.addEventListener('click', function() {
                        markNotificationRead(notification.id, link);
                    });

                    notificationList.appendChild(notificationItem);
                });

                if (unreadCount > 0) {
                    notificationBadge.textContent = unreadCount;
                    notificationBadge.classList.remove('hidden');
                } else {
                    notificationBadge.classList.add('hidden');
                }

                lucide.createIcons();
            })
            .catch(() => {});
        }

        function markNotificationRead(id, link) {
            fetch(`<?= base_url('admin/notifications') ?>/${id}/read`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).finally(() => {
                if (link) {
                    window.location.href = link;
                }
                loadNotifications();
            });
        }
        
        function getNotificationIcon(type) {
            switch(type) {
                case 'danger': return 'alert-triangle';
                case 'warning': return 'alert-circle';
                case 'success': return 'check-circle';
                case 'info': return 'info';
                default: return 'bell';
            }
        }
        
        function viewAllNotifications() {
            window.location.href = '<?= base_url('admin/notifications') ?>';
        }
        
        // Close notification dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('notificationDropdown');
            const bellBtn = document.getElementById('notificationBtn');
            const bellIcon = bellBtn?.querySelector('i');
            
            // Don't close if clicking on recipient dropdown in messages
            if (event.target.closest('#recipientSearch') || event.target.closest('#userDropdown') || event.target.closest('#dropdownToggle')) {
                return;
            }
            
            if (bellBtn && !bellBtn.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // User dropdown functionality
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const userProfile = event.target.closest('.relative:not([style*="z-index"])');
            
            // Don't close if clicking on recipient dropdown in messages
            if (event.target.closest('#recipientSearch') || event.target.closest('#userDropdown') || event.target.closest('#dropdownToggle')) {
                return;
            }
            
            if (!userProfile && !dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
            }
        });
        
        // Sidebar functionality
        const mobileOverlay = document.getElementById('mobileOverlay');
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebarToggle = document.getElementById('sidebarToggle');

        let isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        let isMobileMenuOpen = false;

        // Initialize sidebar state on load
        function initializeSidebarState() {
            if (isCollapsed) {
                sidebar.classList.remove('sidebar-expanded');
                sidebar.classList.add('sidebar-collapsed');
            } else {
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.add('sidebar-expanded');
            }
        }
        
        // Mobile menu toggle
        mobileMenuBtn.addEventListener('click', () => {
            isMobileMenuOpen = !isMobileMenuOpen;
            
            if (isMobileMenuOpen) {
                sidebar.classList.add('open');
                mobileOverlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.remove('open');
                mobileOverlay.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        });
        
        // Close mobile menu when clicking overlay
        mobileOverlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            mobileOverlay.classList.add('hidden');
            document.body.style.overflow = 'auto';
            isMobileMenuOpen = false;
        });
        
        // Desktop sidebar toggle
        sidebarToggle.addEventListener('click', () => {
            isCollapsed = !isCollapsed;
            localStorage.setItem('sidebarCollapsed', isCollapsed.toString());

            if (isCollapsed) {
                sidebar.classList.remove('sidebar-expanded');
                sidebar.classList.add('sidebar-collapsed');

                // Close all open submenus when collapsing
                document.querySelectorAll('.submenu.open').forEach(menu => {
                    menu.classList.remove('open');
                    const chevronId = menu.id.replace('-submenu', '-chevron');
                    const chevron = document.getElementById(chevronId);
                    if (chevron) {
                        chevron.classList.remove('rotated');
                    }
                });
            } else {
                sidebar.classList.remove('sidebar-collapsed');
                sidebar.classList.add('sidebar-expanded');

                // Re-open the active section's submenu when expanding
                const activeItems = document.querySelectorAll('.nav-item.active');
                activeItems.forEach(item => {
                    const submenuId = item.querySelector('[onclick*="toggleSubmenu"]')?.getAttribute('onclick')?.match(/'([^']+)'/)?.[1];
                    if (submenuId) {
                        const submenu = document.getElementById(submenuId);
                        const chevron = document.getElementById(submenuId.replace('-submenu', '-chevron'));
                        if (submenu && chevron) {
                            submenu.classList.add('open');
                            chevron.classList.add('rotated');
                        }
                    }
                });
            }
        });

        // Close mobile menu when clicking on nav items
        const navItems = sidebar.querySelectorAll('nav a');
        navItems.forEach(item => {
            item.addEventListener('click', () => {
                if (window.innerWidth < 768) {
                    sidebar.classList.remove('open');
                    mobileOverlay.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                    isMobileMenuOpen = false;
                }
            });
        });

        // Initialize sidebar state after DOM is ready
        initializeSidebarState();

        // Client Statistics Chart
        const clientChart = document.getElementById('clientChart');
        if (clientChart) {
            const clientCtx = clientChart.getContext('2d');
            new Chart(clientCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                    datasets: [{
                        label: 'US',
                        data: [30, 35, 40, 45, 55, 50, 60],
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'UK',
                        data: [25, 30, 35, 40, 50, 45, 55],
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Site Health Chart
        const siteHealthChart = document.getElementById('siteHealthChart');
        if (siteHealthChart) {
            const siteCtx = siteHealthChart.getContext('2d');
            new Chart(siteCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [84, 16],
                        backgroundColor: ['#6366f1', '#e5e7eb'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // Online Sales Chart
        const onlineSalesChart = document.getElementById('onlineSalesChart');
        if (onlineSalesChart) {
            const salesCtx = onlineSalesChart.getContext('2d');
            new Chart(salesCtx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [80, 20],
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    </script>
    <?= $this->renderSection('js') ?>
    <?= $this->renderSection('scripts') ?>
</body>
</html>

