<?php

namespace App\Controllers\Accounting;

use App\Controllers\BaseController;
use App\Models\JournalEntryModel;
use App\Models\JournalEntryLineModel;
use App\Models\ChartOfAccountsModel;
use App\Models\ProjectModel;

class JournalEntriesController extends BaseController
{
    protected $journalEntryModel;
    protected $journalEntryLineModel;
    protected $chartOfAccountsModel;
    protected $projectModel;

    public function __construct()
    {
        $this->journalEntryModel = new JournalEntryModel();
        $this->journalEntryLineModel = new JournalEntryLineModel();
        $this->chartOfAccountsModel = new ChartOfAccountsModel();
        $this->projectModel = new ProjectModel();
    }

    public function index()
    {
        $filters = [
            'status' => $this->request->getGet('status'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'search' => $this->request->getGet('search')
        ];

        try {
            // Check if journal_entries table exists
            $db = \Config\Database::connect();
            if (!$db->tableExists('journal_entries')) {
                // Tables don't exist - show setup page
                $data = [
                    'title' => 'Journal Entries Setup Required',
                    'setup_required' => true,
                    'setup_url' => base_url('admin/setup/journal-entries')
                ];
                return view('accounting/journal_entries/setup', $data);
            }

            $data = [
                'title' => 'Journal Entries',
                'entries' => $this->journalEntryModel->getJournalEntriesWithDetails($filters),
                'stats' => [], // Disable stats for now
                'filters' => $filters,
                'setup_required' => false
            ];

            return view('accounting/journal_entries/index', $data);
            
        } catch (\Exception $e) {
            // Log the actual error for debugging
            log_message('error', 'Journal Entries Error: ' . $e->getMessage());
            
            // If any database error occurs, show setup page
            $data = [
                'title' => 'Journal Entries Setup Required',
                'setup_required' => true,
                'setup_url' => base_url('admin/setup/journal-entries'),
                'error_message' => $e->getMessage()
            ];
            return view('accounting/journal_entries/setup', $data);
        }
    }

    public function create()
    {
        $data = [
            'title' => 'Create Journal Entry',
            'accounts' => $this->chartOfAccountsModel->getActiveAccounts(),
            'projects' => $this->projectModel->getActiveProjects(),
            'nextEntryNumber' => $this->journalEntryModel->getNextEntryNumber(),
            'validation' => \Config\Services::validation()
        ];

        return view('accounting/journal_entries/create', $data);
    }

    public function store()
    {
        $rules = [
            'entry_date' => 'required|valid_date',
            'description' => 'required|max_length[1000]',
            'reference_type' => 'permit_empty|max_length[100]',
            'lines' => 'required',
            'lines.*.account_id' => 'required|integer',
            'lines.*.debit_amount' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'lines.*.credit_amount' => 'permit_empty|decimal|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $lines = $this->request->getPost('lines');
        
        // Validate that we have at least 2 lines
        if (count($lines) < 2) {
            return redirect()->back()->withInput()->with('error', 'Journal entry must have at least 2 lines.');
        }

        // Validate debit/credit balance
        $totalDebits = 0;
        $totalCredits = 0;
        $validLines = [];

        foreach ($lines as $line) {
            $debit = floatval($line['debit_amount'] ?? 0);
            $credit = floatval($line['credit_amount'] ?? 0);
            
            // Skip empty lines
            if ($debit == 0 && $credit == 0) {
                continue;
            }
            
            // Validate line has either debit or credit, not both
            if ($debit > 0 && $credit > 0) {
                return redirect()->back()->withInput()->with('error', 'Each line must have either a debit OR credit amount, not both.');
            }
            
            $totalDebits += $debit;
            $totalCredits += $credit;
            $validLines[] = $line;
        }

        // Check if debits equal credits
        if (abs($totalDebits - $totalCredits) > 0.01) {
            return redirect()->back()->withInput()->with('error', 'Total debits must equal total credits. Debits: MWK ' . number_format($totalDebits, 2) . ', Credits: MWK ' . number_format($totalCredits, 2));
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create journal entry header
            $entryData = [
                'company_id' => session('company_id') ?? 1,
                'entry_number' => $this->request->getPost('entry_number') ?: $this->journalEntryModel->getNextEntryNumber(),
                'entry_date' => $this->request->getPost('entry_date'),
                'reference_type' => $this->request->getPost('reference_type'),
                'reference_id' => $this->request->getPost('reference_id'),
                'description' => $this->request->getPost('description'),
                'total_debit' => $totalDebits,
                'total_credit' => $totalCredits,
                'status' => $this->request->getPost('save_as_draft') ? 'draft' : 'posted',
                'created_by' => session('user_id') ?? 1
            ];

            // If posting immediately, add posted info
            if (!$this->request->getPost('save_as_draft')) {
                $entryData['posted_at'] = date('Y-m-d H:i:s');
                $entryData['posted_by'] = session('user_id') ?? 1;
            }

            $entryId = $this->journalEntryModel->insert($entryData, true);

            if (!$entryId) {
                throw new \Exception('Failed to create journal entry');
            }

            // Insert journal lines
            if (!$this->journalEntryLineModel->insertJournalLines($entryId, $validLines)) {
                throw new \Exception('Failed to create journal entry lines');
            }

            $db->transCommit();

            $status = $this->request->getPost('save_as_draft') ? 'draft' : 'posted';
            return redirect()->to('/admin/accounting/journal-entries')->with('success', 'Journal entry created successfully and ' . $status . '.');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Failed to create journal entry: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $entry = $this->journalEntryModel->getJournalEntryWithLines($id);
        
        if (!$entry) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Journal entry not found');
        }

        $data = [
            'title' => 'Journal Entry Details - ' . $entry['entry_number'],
            'entry' => $entry
        ];

        return view('accounting/journal_entries/show', $data);
    }

    public function edit($id)
    {
        $entry = $this->journalEntryModel->getJournalEntryWithLines($id);
        
        if (!$entry) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Journal entry not found');
        }

        // Only allow editing of draft entries
        if ($entry['status'] !== 'draft') {
            return redirect()->back()->with('error', 'Only draft journal entries can be edited.');
        }

        $data = [
            'title' => 'Edit Journal Entry - ' . $entry['entry_number'],
            'entry' => $entry,
            'accounts' => $this->chartOfAccountsModel->getActiveAccounts(),
            'projects' => $this->projectModel->getActiveProjects(),
            'validation' => \Config\Services::validation()
        ];

        return view('accounting/journal_entries/edit', $data);
    }

    public function update($id)
    {
        $entry = $this->journalEntryModel->find($id);
        
        if (!$entry) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Journal entry not found');
        }

        // Only allow editing of draft entries
        if ($entry['status'] !== 'draft') {
            return redirect()->back()->with('error', 'Only draft journal entries can be edited.');
        }

        $rules = [
            'entry_date' => 'required|valid_date',
            'description' => 'required|max_length[1000]',
            'reference_type' => 'permit_empty|max_length[100]',
            'lines' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $lines = $this->request->getPost('lines');
        
        // Validate and process lines (same as store method)
        $totalDebits = 0;
        $totalCredits = 0;
        $validLines = [];

        foreach ($lines as $line) {
            $debit = floatval($line['debit_amount'] ?? 0);
            $credit = floatval($line['credit_amount'] ?? 0);
            
            if ($debit == 0 && $credit == 0) continue;
            
            if ($debit > 0 && $credit > 0) {
                return redirect()->back()->withInput()->with('error', 'Each line must have either a debit OR credit amount, not both.');
            }
            
            $totalDebits += $debit;
            $totalCredits += $credit;
            $validLines[] = $line;
        }

        if (abs($totalDebits - $totalCredits) > 0.01) {
            return redirect()->back()->withInput()->with('error', 'Total debits must equal total credits.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update journal entry header
            $entryData = [
                'entry_date' => $this->request->getPost('entry_date'),
                'reference_type' => $this->request->getPost('reference_type'),
                'reference_id' => $this->request->getPost('reference_id'),
                'description' => $this->request->getPost('description'),
                'total_debit' => $totalDebits,
                'total_credit' => $totalCredits
            ];

            $this->journalEntryModel->update($id, $entryData);

            // Delete existing lines and insert new ones
            $this->journalEntryLineModel->deleteJournalLines($id);
            $this->journalEntryLineModel->insertJournalLines($id, $validLines);

            $db->transCommit();

            return redirect()->to('/admin/accounting/journal-entries/' . $id)->with('success', 'Journal entry updated successfully.');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Failed to update journal entry: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $entry = $this->journalEntryModel->find($id);
        
        if (!$entry) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Journal entry not found');
        }

        // Only allow deleting draft entries
        if ($entry['status'] !== 'draft') {
            return redirect()->back()->with('error', 'Only draft journal entries can be deleted.');
        }

        if ($this->journalEntryModel->delete($id)) {
            return redirect()->to('/admin/accounting/journal-entries')->with('success', 'Journal entry deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to delete journal entry.');
        }
    }

    public function post($id)
    {
        $entry = $this->journalEntryModel->find($id);
        
        if (!$entry) {
            return $this->response->setJSON(['success' => false, 'message' => 'Journal entry not found']);
        }

        if ($entry['status'] !== 'draft') {
            return $this->response->setJSON(['success' => false, 'message' => 'Only draft entries can be posted']);
        }

        // Validate balance
        if (!$this->journalEntryModel->validateJournalBalance($id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Journal entry is not balanced']);
        }

        if ($this->journalEntryModel->postJournalEntry($id, session('user_id'))) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Journal entry posted successfully'
            ]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to post journal entry']);
        }
    }

    public function reverse($id)
    {
        $entry = $this->journalEntryModel->find($id);
        
        if (!$entry) {
            return $this->response->setJSON(['success' => false, 'message' => 'Journal entry not found']);
        }

        if ($entry['status'] !== 'posted') {
            return $this->response->setJSON(['success' => false, 'message' => 'Only posted entries can be reversed']);
        }

        $reversalId = $this->journalEntryModel->reverseJournalEntry($id, session('user_id'));
        
        if ($reversalId) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Journal entry reversed successfully',
                'reversal_id' => $reversalId
            ]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to reverse journal entry']);
        }
    }

    /**
     * AJAX endpoint to get account details
     */
    public function getAccount($id)
    {
        $account = $this->chartOfAccountsModel->find($id);
        
        if ($account) {
            return $this->response->setJSON([
                'success' => true,
                'account' => [
                    'id' => $account['id'],
                    'code' => $account['account_code'],
                    'name' => $account['account_name'],
                    'type' => $account['account_type']
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Account not found'
            ]);
        }
    }
}