<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\ChartOfAccountsModel;
use App\Models\JournalEntryLineModel;
use App\Models\AccountCategoryModel;

class GeneralLedgerController extends BaseController
{
    protected $chartOfAccountsModel;
    protected $journalEntryLineModel;
    protected $accountCategoryModel;

    public function __construct()
    {
        $this->chartOfAccountsModel = new ChartOfAccountsModel();
        $this->journalEntryLineModel = new JournalEntryLineModel();
        $this->accountCategoryModel = new AccountCategoryModel();
    }

    public function index()
    {
        $filters = [
            'account_id' => $this->request->getGet('account_id'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'account_type' => $this->request->getGet('account_type'),
            'category_id' => $this->request->getGet('category_id')
        ];

        try {
            $accounts = $this->chartOfAccountsModel->getAccountsWithBalances($filters);
            $categories = $this->accountCategoryModel->getActiveCategories();
            $totals = $this->calculateTotals($filters);
            
            $data = [
                'title' => 'General Ledger',
                'accounts' => $accounts ?? [],
                'categories' => $categories ?? [],
                'filters' => $filters,
                'totals' => $totals ?? []
            ];

            return view('accounting/general_ledger/index', $data);

        } catch (\Exception $e) {
            log_message('error', 'General Ledger Error: ' . $e->getMessage());
            
            $data = [
                'title' => 'General Ledger',
                'accounts' => [],
                'categories' => $this->accountCategoryModel->findAll(), // Use simple findAll as fallback
                'filters' => $filters,
                'totals' => [
                    'total_assets' => 0,
                    'total_liabilities' => 0,
                    'total_equity' => 0,
                    'total_revenue' => 0,
                    'total_expenses' => 0
                ],
                'error_message' => 'Unable to load general ledger data: ' . $e->getMessage()
            ];
            
            return view('accounting/general_ledger/index', $data);
        }
    }

    public function account($accountId)
    {
        $filters = [
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        try {
            $account = $this->chartOfAccountsModel->find($accountId);
            
            if (!$account) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Account not found');
            }

            $transactions = $this->journalEntryLineModel->getAccountTransactions($accountId, $filters);
            $balance = $this->journalEntryLineModel->getAccountBalance($accountId, $filters['date_to']);

            $data = [
                'title' => 'Account Ledger - ' . $account['account_name'],
                'account' => $account,
                'transactions' => $transactions,
                'balance' => $balance,
                'filters' => $filters
            ];

            return view('accounting/general_ledger/account', $data);

        } catch (\Exception $e) {
            log_message('error', 'Account Ledger Error: ' . $e->getMessage());
            return redirect()->to('/admin/accounting/general-ledger')->with('error', $e->getMessage());
        }
    }

    public function trialBalance()
    {
        $asOfDate = $this->request->getGet('as_of_date') ?: date('Y-m-d');

        try {
            $trialBalanceData = $this->journalEntryLineModel->getTrialBalance($asOfDate);
            
            $data = [
                'title' => 'Trial Balance',
                'trial_balance' => $trialBalanceData,
                'as_of_date' => $asOfDate,
                'totals' => $this->calculateTrialBalanceTotals($trialBalanceData)
            ];

            return view('accounting/general_ledger/trial_balance', $data);

        } catch (\Exception $e) {
            log_message('error', 'Trial Balance Error: ' . $e->getMessage());
            
            $data = [
                'title' => 'Trial Balance',
                'trial_balance' => [],
                'as_of_date' => $asOfDate,
                'totals' => ['total_debits' => 0, 'total_credits' => 0],
                'error_message' => 'Unable to generate trial balance: ' . $e->getMessage()
            ];
            
            return view('accounting/general_ledger/trial_balance', $data);
        }
    }

    private function calculateTotals($filters)
    {
        $accounts = $this->chartOfAccountsModel->getAccountsWithBalances($filters);
        
        $totals = [
            'total_assets' => 0,
            'total_liabilities' => 0,
            'total_equity' => 0,
            'total_revenue' => 0,
            'total_expenses' => 0
        ];

        foreach ($accounts as $account) {
            $balance = floatval($account['balance'] ?? 0);
            
            switch ($account['account_type']) {
                case 'asset':
                    $totals['total_assets'] += $balance;
                    break;
                case 'liability':
                    $totals['total_liabilities'] += $balance;
                    break;
                case 'equity':
                    $totals['total_equity'] += $balance;
                    break;
                case 'revenue':
                    $totals['total_revenue'] += $balance;
                    break;
                case 'expense':
                    $totals['total_expenses'] += $balance;
                    break;
            }
        }

        return $totals;
    }

    private function calculateTrialBalanceTotals($trialBalanceData)
    {
        $totalDebits = 0;
        $totalCredits = 0;

        foreach ($trialBalanceData as $account) {
            $totalDebits += floatval($account['debit_balance'] ?? 0);
            $totalCredits += floatval($account['credit_balance'] ?? 0);
        }

        return [
            'total_debits' => $totalDebits,
            'total_credits' => $totalCredits,
            'difference' => $totalDebits - $totalCredits
        ];
    }
}