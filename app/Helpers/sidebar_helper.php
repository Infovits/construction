<?php

if (!function_exists('canViewDashboard')) {
    function canViewDashboard() {
        return hasPermission('dashboard.view') || hasPermission('*');
    }
}

if (!function_exists('canViewProjects')) {
    function canViewProjects() {
        return hasPermission('projects.view') || hasPermission('projects.*') || hasPermission('*');
    }
}

if (!function_exists('canViewProjectCategories')) {
    function canViewProjectCategories() {
        return hasPermission('project_categories.view') || hasPermission('project_categories.*') || hasPermission('*');
    }
}

if (!function_exists('canViewMilestones')) {
    function canViewMilestones() {
        return hasPermission('milestones.view') || hasPermission('milestones.*') || hasPermission('*');
    }
}

if (!function_exists('canViewTasks')) {
    function canViewTasks() {
        return hasPermission('tasks.view') || hasPermission('tasks.*') || hasPermission('*');
    }
}

if (!function_exists('canViewClients')) {
    function canViewClients() {
        return hasPermission('clients.view') || hasPermission('clients.*') || hasPermission('*');
    }
}

if (!function_exists('canViewMessages')) {
    function canViewMessages() {
        return hasPermission('messages.view') || hasPermission('messages.*') || hasPermission('*');
    }
}

if (!function_exists('canViewNotifications')) {
    function canViewNotifications() {
        return hasPermission('notifications.view') || hasPermission('notifications.*') || hasPermission('*');
    }
}

if (!function_exists('canViewOverview')) {
    function canViewOverview() {
        return hasPermission('overview.view') || hasPermission('*');
    }
}

if (!function_exists('canViewAnalytics')) {
    function canViewAnalytics() {
        return hasPermission('analytics.view') || hasPermission('analytics.*') || hasPermission('*');
    }
}

if (!function_exists('canViewGitCommits')) {
    function canViewGitCommits() {
        return hasPermission('gitcommits.view') || hasPermission('gitcommits.*') || hasPermission('*');
    }
}

if (!function_exists('canViewInventory')) {
    function canViewInventory() {
        return hasPermission('materials.view') || hasPermission('materials.*') || 
               hasPermission('inventory.view') || hasPermission('inventory.*') || hasPermission('*');
    }
}

if (!function_exists('canViewMaterials')) {
    function canViewMaterials() {
        return hasPermission('materials.view') || hasPermission('materials.*') || hasPermission('*');
    }
}

if (!function_exists('canViewMaterialCategories')) {
    function canViewMaterialCategories() {
        return hasPermission('material_categories.view') || hasPermission('material_categories.*') || hasPermission('*');
    }
}

if (!function_exists('canViewWarehouses')) {
    function canViewWarehouses() {
        return hasPermission('warehouses.view') || hasPermission('warehouses.*') || hasPermission('*');
    }
}

if (!function_exists('canViewSuppliers')) {
    function canViewSuppliers() {
        return hasPermission('suppliers.view') || hasPermission('suppliers.*') || hasPermission('*');
    }
}

if (!function_exists('canViewFiles')) {
    function canViewFiles() {
        return hasPermission('files.view') || hasPermission('files.*') || hasPermission('*');
    }
}

if (!function_exists('canViewSafety')) {
    function canViewSafety() {
        return hasPermission('safety.view') || hasPermission('safety.*') || hasPermission('*');
    }
}

if (!function_exists('canViewProcurement')) {
    function canViewProcurement() {
        return hasPermission('procurement.view') || hasPermission('procurement.*') || 
               hasPermission('material_requests.view') || hasPermission('purchase_orders.view') || 
               hasPermission('goods_receipt.view') || hasPermission('quality_inspections.view') || hasPermission('*');
    }
}

if (!function_exists('canViewMaterialRequests')) {
    function canViewMaterialRequests() {
        return hasPermission('material_requests.view') || hasPermission('material_requests.*') || hasPermission('*');
    }
}

if (!function_exists('canViewPurchaseOrders')) {
    function canViewPurchaseOrders() {
        return hasPermission('purchase_orders.view') || hasPermission('purchase_orders.*') || hasPermission('*');
    }
}

if (!function_exists('canViewGoodsReceipt')) {
    function canViewGoodsReceipt() {
        return hasPermission('goods_receipt.view') || hasPermission('goods_receipt.*') || hasPermission('*');
    }
}

if (!function_exists('canViewQualityInspections')) {
    function canViewQualityInspections() {
        return hasPermission('quality_inspections.view') || hasPermission('quality_inspections.*') || hasPermission('*');
    }
}

if (!function_exists('canViewAccounting')) {
    function canViewAccounting() {
        return hasPermission('accounting.view') || hasPermission('accounting.*') || 
               hasPermission('chart_of_accounts.view') || hasPermission('journal_entries.view') || 
               hasPermission('general_ledger.view') || hasPermission('cost_codes.view') || 
               hasPermission('job_budgets.view') || hasPermission('job_cost_tracking.view') || hasPermission('*');
    }
}

if (!function_exists('canViewChartOfAccounts')) {
    function canViewChartOfAccounts() {
        return hasPermission('chart_of_accounts.view') || hasPermission('chart_of_accounts.*') || hasPermission('*');
    }
}

if (!function_exists('canViewAccountCategories')) {
    function canViewAccountCategories() {
        return hasPermission('account_categories.view') || hasPermission('account_categories.*') || hasPermission('*');
    }
}

if (!function_exists('canViewJournalEntries')) {
    function canViewJournalEntries() {
        return hasPermission('journal_entries.view') || hasPermission('journal_entries.*') || hasPermission('*');
    }
}

if (!function_exists('canViewGeneralLedger')) {
    function canViewGeneralLedger() {
        return hasPermission('general_ledger.view') || hasPermission('general_ledger.*') || hasPermission('*');
    }
}

if (!function_exists('canViewCostCodes')) {
    function canViewCostCodes() {
        return hasPermission('cost_codes.view') || hasPermission('cost_codes.*') || hasPermission('*');
    }
}

if (!function_exists('canViewJobBudgets')) {
    function canViewJobBudgets() {
        return hasPermission('job_budgets.view') || hasPermission('job_budgets.*') || hasPermission('*');
    }
}

if (!function_exists('canViewJobCostTracking')) {
    function canViewJobCostTracking() {
        return hasPermission('job_cost_tracking.view') || hasPermission('job_cost_tracking.*') || hasPermission('*');
    }
}

if (!function_exists('canViewHR')) {
    function canViewHR() {
        return hasPermission('hr.view') || hasPermission('users.view') || hasPermission('roles.view') || 
               hasPermission('departments.view') || hasPermission('positions.view') || hasPermission('*');
    }
}

if (!function_exists('canViewDepartments')) {
    function canViewDepartments() {
        return hasPermission('departments.view') || hasPermission('departments.*') || hasPermission('*');
    }
}

if (!function_exists('canViewPositions')) {
    function canViewPositions() {
        return hasPermission('positions.view') || hasPermission('positions.*') || hasPermission('*');
    }
}

if (!function_exists('canViewSettings')) {
    function canViewSettings() {
        return hasPermission('settings.view') || hasPermission('settings.*') || hasPermission('*');
    }
}

if (!function_exists('canViewReports')) {
    function canViewReports() {
        return hasPermission('reports.view') || hasPermission('reports.*') || hasPermission('*');
    }
}
